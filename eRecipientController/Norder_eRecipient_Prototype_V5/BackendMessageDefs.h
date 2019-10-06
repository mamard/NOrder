#pragma once

using namespace std;

#include <list>
#include <iterator>

#define ID_RESPONSE_TYPE "ID_RESPONSE"
#define STAY_ALIVE_TYPE "STAY_ALIVE"
#define SET_SLEEP_PERIOD_TYPE "SET_SLEEP_PERIOD"
#define SET_TARE_TYPE "SET_TARE"
#define SET_WIFI_CONFIGURATION_TYPE "SET_WIFI_CONFIGURATION"
#define OFFSET_CALIBRATION_TYPE "OFFSET_CALIBRATION"
#define SCALE_CALIBRATION_TYPE "SCALE_CALIBRATION"
#define SCALE_QUERY_TYPE "SCALE_QUERY"
#define BATTERY_LEVEL_QUERY_TYPE "BATTERY_LEVEL_QUERY"
#define GO_TO_SLEEP_TYPE "GO_TO_SLEEP"

#define ID_REQUEST_TYPE "ID_REQUEST"
#define STAY_ALIVE_ACK_TYPE "STAY_ALIVE_ACK"
#define SET_SLEEP_PERIOD_ACK_TYPE "SET_SLEEP_PERIOD_ACK"
#define SET_TARE_ACK_TYPE "SET_TARE_ACK"
#define SET_WIFI_CONFIGURATION_ACK_TYPE "SET_WIFI_CONFIGURATION_ACK"
#define OFFSET_CALIBRATION_ACK_TYPE "OFFSET_CALIBRATION_ACK"
#define SCALE_CALIBRATION_ACK_TYPE "SCALE_CALIBRATION_ACK"
#define SCALE_OUTPUT_MEASURE_TYPE "SCALE_OUTPUT_MEASURE"
#define SCALE_MEASURE_DONE_TYPE "SCALE_MEASURE_DONE"
#define BATTERY_LEVEL_MEASURE_TYPE "BATTERY_LEVEL_MEASURE"

#define DEBUG_MESSAGE_TYPE_INFO "This is a %s message."

typedef enum  {
  ID_RESPONSE,
  STAY_ALIVE,
  SET_SLEEP_PERIOD,
  SET_TARE,
  SET_WIFI_CONFIGURATION,
  OFFSET_CALIBRATION,
  SCALE_CALIBRATION,
  SCALE_QUERY,
  BATTERY_LEVEL_QUERY,
  GO_TO_SLEEP,
  ID_REQUEST,
  STAY_ALIVE_ACK,
  SET_SLEEP_PERIOD_ACK,
  SET_TARE_ACK,
  SET_WIFI_CONFIGURATION_ACK,
  OFFSET_CALIBRATION_ACK,
  SCALE_CALIBRATION_ACK,
  SCALE_OUTPUT_MEASURE,
  SCALE_MEASURE_DONE,
  BATTERY_LEVEL_MEASURE,
  UNKNOWN_TYPE
} backend_message_type;

typedef struct _backend_message_param{
	String key;
	JsonVariant value;

	_backend_message_param(String pName, JsonVariant pValue) : key(pName), value(pValue) {};
	_backend_message_param(const _backend_message_param& other)
	{
	 	this->key = other.key;
	 	this->value = other.value;
	};
	_backend_message_param& operator = (const _backend_message_param &rhs) 
	{ 
		if (this != &rhs){
		   this->key = rhs.key;
		   this->value = rhs.value;
		}
		
		return *this; 
	};
} backend_message_param;

typedef list<backend_message_param> backend_message_params_list;

typedef struct _backend_message{
	backend_message_type type;
	backend_message_params_list params;

  _backend_message() : type(UNKNOWN_TYPE) {};
  _backend_message(backend_message_type eType) : type(eType) {};
	
	bool hasParameters(){return params.size() > 0;};
	void printParameters()
	{
		int paramNum = 0;
		Serial.println("List of message parameters : ");
		for (auto kv : params)
		{
	   		char paramInfo[256];
	   		sprintf(paramInfo, "Parameter %d : key : %s - value : %s", ++paramNum, kv.key.c_str(), kv.value.as<char*>());
	   		Serial.println(paramInfo);
		}
	};
	void addParam(String pName, JsonVariant pValue){params.push_back(backend_message_param(pName, pValue));};
} backend_message;
