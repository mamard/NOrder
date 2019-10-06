using namespace std;

#include <map>
#include <algorithm>

#include "FS.h"
#include "SPIFFS.h"

#include "EEPROM.h"

#include <pthread.h>

#include "BackendMessageSender.h"
#include "JsonResponseParser.h"

#include "HX711.h"

#define EEPROM_SIZE 64
#define SCALE_VALUE_ADDRESS  0
#define OFFSET_VALUE_ADDRESS  sizeof(float)
#define DEEP_SLEEP_PERIOD_ADDRESS OFFSET_VALUE_ADDRESS + sizeof(long)

#define BACKEND_REMOTE_PORT 11500
#define CALIBRATION_SERVICE_REMOTE_PORT 11501

#define DEFAULT_CALIBRATED_SCALE_VALUE  0.002f
#define DEFAULT_CALIBRATED_OFFSET_VALUE -2000
#define DEFAULT_SLEEPING_PERIOD 60
#define SLEEPING_PERIOD_WHEN_BACKEND_CONNECTION_FAILED 300

#define uS_TO_S_FACTOR 1000000ULL  /* Conversion factor for micro seconds to seconds */
#define TIME_TO_SLEEP  5        /* Time ESP32 will go to sleep*/

#define MAX_BACKEND_MESSAGE_SIZE 4096
#define MAX_BACKEND_CONNECTION_ATTEMPTS 20

#define WAKE_UP_TOUCH_PAD T4
#define TOUCH_PAD_THRESHOLD 40

typedef bool (*handle_message_func)(backend_message_params_list, BackendMessageSender*);
typedef std::map<backend_message_type, handle_message_func> handle_message_actions_map;
typedef struct _param_key_equality_predicate{
  String key;
  _param_key_equality_predicate(String pKey) : key(pKey){};
  bool operator ()(const backend_message_param& param) {return param.key == key;};
} param_key_equality_predicate;

RTC_DATA_ATTR int bootCount = 0;
touch_pad_t touchPin;

int waitBeforeSleepingPeriod = 10 * 60; //

const int LOADCELL_DOUT_PIN = 25;
const int LOADCELL_SCK_PIN = 26;

const int DEEP_SLEEP_CONTACT_PIN = 13;
const int VOLTAGE_INPUT_ANALOG_PIN = 12;

typedef struct _wifi_config{
  String ssid = "";
  String pwd = "";

  _wifi_config(String pSsid, String pPwd) : ssid(pSsid), pwd(pPwd) {};
  
} wifi_config;

list<wifi_config> wifi_configurations;

bool bIsEEPROMAvailable;
long lCalibratedOffset;
float fCalibratedScale;
long lDeepSleepingPeriod;

WiFiClient tcp_client;
handle_message_actions_map handleMessageActionsMap;
int remoteServerPort;
    
HX711 scale;

bool parseJsonConfigFile()
{
  bool parseOk = true;

  // start filesystem 
  if(!SPIFFS.begin(true)){
      Serial.println("SPIFFS Mount Failed");
      return false;
  }

  File fConfig = SPIFFS.open("/config.json", "r");
  if(!fConfig) {
    return false;
  }

  char jsonConfig[256];
  sprintf(jsonConfig, fConfig.readString().c_str());

  StaticJsonBuffer<256> jsonBuf;
  JsonObject& root = jsonBuf.parseObject(jsonConfig);
  
  if (!root.success())
  {
    parseOk = false;
    Serial.println("Failed to read file, using default configuration");
  }
  else {  
    JsonObject& wiFiSection = root["WiFi"];
    
    for (auto kv : wiFiSection){
      
      String wifi_ssid = wiFiSection[kv.key]["ssid"].as<String>();
      String wifi_pwd = wiFiSection[kv.key]["password"].as<String>();

      wifi_configurations.push_back(wifi_config(wifi_ssid, wifi_pwd));
    }
  }
  
  return parseOk;
}

void callback(){
  //placeholder callback function
  Serial.println("Touchpad T4 pressed.");
}

void print_wakeup_reason(){
  esp_sleep_wakeup_cause_t wakeup_reason;

  wakeup_reason = esp_sleep_get_wakeup_cause();

  switch(wakeup_reason){
    case ESP_SLEEP_WAKEUP_EXT0 : Serial.println("Wakeup caused by external signal using RTC_IO"); break;
    case ESP_SLEEP_WAKEUP_EXT1 : Serial.println("Wakeup caused by external signal using RTC_CNTL"); break;
    case ESP_SLEEP_WAKEUP_TIMER : Serial.println("Wakeup caused by timer"); break;
    case ESP_SLEEP_WAKEUP_TOUCHPAD : Serial.println("Wakeup caused by touchpad"); break;
    case ESP_SLEEP_WAKEUP_ULP : Serial.println("Wakeup caused by ULP program"); break;
    default : Serial.printf("Wakeup was not caused by deep sleep: %d\n",wakeup_reason); break;
  }
}

bool WiFiConnect(const char* ssid, const char *password){

   int maxWaitings = 25;
   int nTry = 0;
   
   Serial.println();
   Serial.print("Connecting to ");
   Serial.println(ssid);

   WiFi.mode(WIFI_STA);
   WiFi.begin(ssid, password);
   
   //tcpip_adapter_set_hostname(TCPIP_ADAPTER_IF_STA, "NOrder_Controller");
   int wifiStatus = -1;
   while ((wifiStatus = WiFi.status()) != WL_CONNECTED && nTry < maxWaitings) {
       delay(500);
       Serial.print(".");
       nTry ++;
   }

   return wifiStatus == WL_CONNECTED;
}

bool has_eeprom_been_user_modified(){
  bool hasBeenModified = false;
  int sizeToRead = OFFSET_VALUE_ADDRESS + sizeof(long);

  int i =0;
  while (!hasBeenModified && i < sizeToRead){
    if (EEPROM.read(i) < 255)
      hasBeenModified = true;
    ++i;
  }

  return hasBeenModified;
}

void readInitialEEPROMConfig()
{
   bIsEEPROMAvailable = EEPROM.begin(EEPROM_SIZE);
   
   //Initialize the EEPROM API
   if (!bIsEEPROMAvailable)
   {
       lCalibratedOffset = DEFAULT_CALIBRATED_OFFSET_VALUE;
       fCalibratedScale =  DEFAULT_CALIBRATED_SCALE_VALUE;
       lDeepSleepingPeriod = DEFAULT_SLEEPING_PERIOD;
   }
   else {
      bool userModifiedEEPROM = has_eeprom_been_user_modified();
      
      fCalibratedScale =  userModifiedEEPROM ? EEPROM.readFloat(SCALE_VALUE_ADDRESS) : DEFAULT_CALIBRATED_SCALE_VALUE;
      lCalibratedOffset = userModifiedEEPROM ? EEPROM.readLong(OFFSET_VALUE_ADDRESS) : DEFAULT_CALIBRATED_OFFSET_VALUE;  
      lDeepSleepingPeriod = userModifiedEEPROM && EEPROM.readLong(DEEP_SLEEP_PERIOD_ADDRESS) != -1 ? EEPROM.readLong(DEEP_SLEEP_PERIOD_ADDRESS) : DEFAULT_SLEEPING_PERIOD;
   }
}

void initScaleSensor(){
  scale.begin(LOADCELL_DOUT_PIN, LOADCELL_SCK_PIN);

  scale.set_offset(lCalibratedOffset);
  scale.set_scale(fCalibratedScale);
}

int read_next_mass_value(){
   return round(scale.get_units(20));
}

int read_analog_voltage_value(){
   return analogRead(VOLTAGE_INPUT_ANALOG_PIN);
}

template<typename T>
bool lookUpParamByKey(backend_message_params_list params, const String& key, T& value) {
  bool found = false;
  
  backend_message_params_list::iterator it;
  it = find_if(params.begin(), params.end(), param_key_equality_predicate(key));

  if (it != params.end()){
     found = true;
     value = (*it).value.as<T>();
  }
  
  return found;
}

bool handle_id_response_message(backend_message_params_list params, BackendMessageSender* sender)
{
   int id_status = -1;
   bool hResult = true;
   
   if ((hResult &= lookUpParamByKey<int>(params, "Status", id_status)))
      Serial.printf("[handle_id_response_message] Status of Identification Response : %d \n", id_status);
      
   return hResult;
}

bool handle_stay_alive_message(backend_message_params_list params, BackendMessageSender* sender)
{
   bool hResult = true;
   
   if ((hResult &= lookUpParamByKey<int>(params, "Period", waitBeforeSleepingPeriod)))
   {
      Serial.printf("[handle_stay_alive_message] Staying Alive Period : %d \n", waitBeforeSleepingPeriod);
      backend_message stayAliveAckMessage(STAY_ALIVE_ACK);
      sender->writeToServer(stayAliveAckMessage);
   }
      
   return hResult;
}

bool handle_set_sleep_period_message(backend_message_params_list params, BackendMessageSender* sender)
{
   bool hResult = true;
   
   if ((hResult &= lookUpParamByKey<long>(params, "Period", lDeepSleepingPeriod))) {
      Serial.printf("[handle_set_sleep_period_message] Deep Sleeping Delay : %d \n", lDeepSleepingPeriod);

      if (bIsEEPROMAvailable) {
        //Write the computed scale calibration 
        EEPROM.writeLong(DEEP_SLEEP_PERIOD_ADDRESS, lDeepSleepingPeriod);
        EEPROM.commit();
      }

       backend_message setSleepPeriodAckMessage(SET_SLEEP_PERIOD_ACK);
       sender->writeToServer(setSleepPeriodAckMessage);
   }
      
   return hResult;
}

bool handle_user_wifi_configuration_message(backend_message_params_list params, BackendMessageSender* sender)
{
    bool hResult = true;

    /*if ((hResult &= lookUpParamByKey<String>(params, "Ssid", wifi_ssid)) &&
        (hResult &= lookUpParamByKey<String>(params, "Password", wifi_pwd)))
    {
       
    }

    backend_message setWifiConfigurationAckMessage(SET_WIFI_CONFIGURATION_ACK);
    sender->writeToServer(setWifiConfigurationAckMessage);*/
    
    return hResult;
}

bool handle_tare_set_message(backend_message_params_list params, BackendMessageSender* sender)
{
   bool hResult = true;

   lCalibratedOffset = scale.read_average(20);

   scale.set_offset(lCalibratedOffset);

   backend_message setTareAckMessage(SET_TARE_ACK);

   if ((hResult &= bIsEEPROMAvailable)){
     //Write the computed scale calibration 
     EEPROM.writeLong(OFFSET_VALUE_ADDRESS, lCalibratedOffset);
     EEPROM.commit();

     setTareAckMessage.addParam("Status", 1); 
   }
   else
   {
     setTareAckMessage.addParam("Status", 0); 
   }

   sender->writeToServer(setTareAckMessage);

   return hResult;
}
bool handle_offset_calibration_message(backend_message_params_list params, BackendMessageSender* sender)
{
   bool hResult = true;
   lCalibratedOffset = scale.read_average(20);

   scale.set_offset(lCalibratedOffset);

   backend_message offsetCalibrationAckMessage(OFFSET_CALIBRATION_ACK);
      

   if ((hResult &= bIsEEPROMAvailable)){
     //Write the computed scale calibration 
     EEPROM.writeLong(OFFSET_VALUE_ADDRESS, lCalibratedOffset);
     EEPROM.commit();

     offsetCalibrationAckMessage.addParam("Status", 1); 
   }
   else
   {
     offsetCalibrationAckMessage.addParam("Status", 0);
   }

   sender->writeToServer(offsetCalibrationAckMessage);

   return hResult;
}
bool handle_scale_calibration_message(backend_message_params_list params, BackendMessageSender* sender)
{
   bool hResult = true;
   int referenceMass = -1;
   
   if (hResult &= lookUpParamByKey<int>(params, "ReferenceMass", referenceMass))
   {
       if (referenceMass == 0)
       {
          hResult &= handle_tare_set_message(params, sender);
       }
       else 
       {
         long lRawCellValue = scale.read_average(20);
         
         fCalibratedScale = (float)((float)(lRawCellValue - lCalibratedOffset) / (float)referenceMass);
  
         scale.set_scale(fCalibratedScale);

         backend_message scaleCalibrationAckMessage(SCALE_CALIBRATION_ACK);

         if ((hResult &= bIsEEPROMAvailable)){
           //Write the computed scale calibration 
           EEPROM.writeFloat(SCALE_VALUE_ADDRESS, fCalibratedScale);
           EEPROM.commit();

           scaleCalibrationAckMessage.addParam("Status", 1); 

         }     
         else {
            scaleCalibrationAckMessage.addParam("Status", 0); 
         }

          sender->writeToServer(scaleCalibrationAckMessage);
       }     
   }
   
   return hResult;
}

bool handle_scale_query_message(backend_message_params_list params, BackendMessageSender* sender)
{
   bool hResult = true;
   
   int eRecipSlot = -1;
   int nbRepeat = 0;
   int nbMeasuresDone = 0;
   
   if ((hResult &= lookUpParamByKey<int>(params, "Slot", eRecipSlot)))
   {
       lookUpParamByKey<int>(params, "Repeat", nbRepeat);
       
       do {
         int nextMass = read_next_mass_value(); 
         
         nbMeasuresDone ++;
         
         backend_message scaleOutputMeasuretMessage(SCALE_OUTPUT_MEASURE);
        
         if (nextMass < 0)
         {
            scaleOutputMeasuretMessage.addParam("Status", 0);
         }
         
         else {
            scaleOutputMeasuretMessage.addParam("Status", 1);
            scaleOutputMeasuretMessage.addParam("Mass", nextMass);
         }
         
         sender->writeToServer(scaleOutputMeasuretMessage);
         
       } while(nbMeasuresDone <= nbRepeat);

       backend_message scaleMeasureDoneMessage(SCALE_MEASURE_DONE);
       sender->writeToServer(scaleMeasureDoneMessage);
   }
   
   return hResult;
}
bool handle_battery_level_query_message(backend_message_params_list params, BackendMessageSender* sender)
{
   int vAnalogValue = read_analog_voltage_value();

   float battery_voltage = (float)((3.3f * vAnalogValue)/4095);
   int battery_level = 7;

   backend_message batteryMeasuretMessage(BATTERY_LEVEL_MEASURE);
   batteryMeasuretMessage.addParam("Level", battery_level);

   sender->writeToServer(batteryMeasuretMessage);
   
   return true;
}

bool handle_go_to_sleep_message(backend_message_params_list params, BackendMessageSender* sender)
{
     
   return true;
}

void initHandleMessageActions(){
  handleMessageActionsMap[ID_RESPONSE] = &handle_id_response_message;
  handleMessageActionsMap[STAY_ALIVE] = &handle_stay_alive_message;
  handleMessageActionsMap[SET_SLEEP_PERIOD] = &handle_set_sleep_period_message;
  handleMessageActionsMap[SET_TARE] = &handle_tare_set_message;
  handleMessageActionsMap[SCALE_QUERY] = &handle_scale_query_message;
  handleMessageActionsMap[BATTERY_LEVEL_QUERY] = &handle_battery_level_query_message;
  handleMessageActionsMap[OFFSET_CALIBRATION] = &handle_offset_calibration_message;
  handleMessageActionsMap[SCALE_CALIBRATION] = &handle_scale_calibration_message;
  handleMessageActionsMap[GO_TO_SLEEP] = &handle_go_to_sleep_message;
}

void setup() {
   // put your setup code here, to run once:
   // put your setup code here, to run once:
   Serial.begin(115200);
   
   Serial.printf("[REBOOT_BEGIN] Free heap available size : %d \n", esp_get_free_heap_size());
   
   pinMode(WAKE_UP_TOUCH_PAD, INPUT_PULLUP);

   delay(500);

   //Increment boot number and print it every reboot
   ++bootCount;
   Serial.println("Boot number: " + String(bootCount));

   print_wakeup_reason();

   initHandleMessageActions();

   readInitialEEPROMConfig();
   
   initScaleSensor();

   remoteServerPort = has_eeprom_been_user_modified() ? BACKEND_REMOTE_PORT : CALIBRATION_SERVICE_REMOTE_PORT;
   Serial.println("Remote server port : " + String(remoteServerPort));
   
   //Read WiFi string connection from config file
   if (parseJsonConfigFile()){
      for (auto wifiConfig : wifi_configurations)
      {
        if (WiFiConnect(wifiConfig.ssid.c_str(), wifiConfig.pwd.c_str())) {
            Serial.println("Connected to Wifi network " + wifiConfig.ssid);
            Serial.println("Remote port connection set to " + remoteServerPort);
            bool connectedToBackend = false;
            int nAttempts = 0;
            do {
                connectedToBackend = tcp_client.connect("<server_dns_name>", remoteServerPort);
                nAttempts++;
                
                if (!connectedToBackend){
                  Serial.println("Connection to backend failed. Retry in 30s ...");
                  delay(30000);
                }
                
            } while (!connectedToBackend && nAttempts < MAX_BACKEND_CONNECTION_ATTEMPTS);
  
            if (connectedToBackend) {
              BackendMessageSender messageSender(&tcp_client);
              
              //Once connected to backend, send a ID_REQUEST message
              uint8_t chipid[6];
              char mac_address[18];
              
              esp_efuse_read_mac(chipid);
              sprintf(mac_address, "%02x:%02x:%02x:%02x:%02x:%02x",chipid[0], chipid[1], chipid[2], chipid[3], chipid[4], chipid[5]);
  
              backend_message idRequestMessage(ID_REQUEST);
              idRequestMessage.addParam("MacAdress", mac_address);
  
              messageSender.writeToServer(idRequestMessage);
                         
              JsonResponseParser messageParser;
              while (tcp_client.connected()) {
                int read_data = tcp_client.available();
                char message[read_data + 1];
                
                if (read_data > 0) {
                    Serial.println("Response from backend ...");
                    for (int i = 0; i < read_data; i++)
                    {
                       message[i] = tcp_client.read();
                    }
                    message[read_data] = '\0';
                    
                    tcp_client.flush();
                    
                    Serial.println("Received message : " + String(message));
  
                    backend_message parsedMessage;
                    if (messageParser.parseMessage(message, parsedMessage)){
                       
                       handle_message_actions_map::iterator funcIt;
                       if ((funcIt = handleMessageActionsMap.find(parsedMessage.type)) != handleMessageActionsMap.end()) {
                          bool hResult = (*funcIt->second)(parsedMessage.params, &messageSender);
                       }
                       else {
                          Serial.println("Message type not handled ...");
                       }
                       
                    }
                    else {
                       Serial.println("Error encountered while parsing the message sent by backend !");
                    }
                }
  
                //Serial.println("Waiting for next message");
                delay(100);
              }
            }
            else {
               lDeepSleepingPeriod = SLEEPING_PERIOD_WHEN_BACKEND_CONNECTION_FAILED;
            }

            break;
        }
      }
   }
   
   //Setup interrupt on Touch Pad 4 (GPIO13)
   touchAttachInterrupt(WAKE_UP_TOUCH_PAD, callback, TOUCH_PAD_THRESHOLD);

   //Configure Touchpad as wakeup source
   esp_sleep_enable_touchpad_wakeup();

   Serial.printf("Deep sleep period in seconds : %d \n", lDeepSleepingPeriod);
   esp_sleep_enable_timer_wakeup(lDeepSleepingPeriod * uS_TO_S_FACTOR);
   
   Serial.println("Going to sleep now ...");

   
   delay(250);
   
   esp_deep_sleep_start();
}

void loop() {
  // Nothing to do
}
