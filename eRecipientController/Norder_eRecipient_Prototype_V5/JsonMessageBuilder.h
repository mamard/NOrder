#include <ArduinoJson.h>

#include "BackendMessageDefs.h"

class JsonMessageBuilder
{
    DynamicJsonBuffer _buffer;
    JsonObject& _root;
    
public:
    JsonMessageBuilder()
        : _root(_buffer.createObject())//, _params(_root.createNestedObject("PARAMS"))
    {
		//_root["TYPE"] = msgType;
    }

  	~JsonMessageBuilder(){
  		_buffer.clear();	
  	}

    bool process(backend_message message){
       bool pResult = true;
       
       String msgType("");

       if (pResult&=translateTypeToString(message.type, msgType)) {
          _root["TYPE"] = msgType.c_str();

          if (message.hasParameters()) {
              JsonObject& params = _root.createNestedObject("PARAMS");

              for (auto kv : message.params) {
                 addParam(kv.key.c_str(), kv.value, params);
              }
          }
       }
       
       return pResult;
    }
    
    //template<typename T>
    void addParam(const char * name, JsonVariant& value, JsonObject& params)
    {
        params[name] = value;           
    }

    bool translateTypeToString(const backend_message_type& eType, String& sType) {
       bool translationResult = true;

       switch (eType) {
          case ID_REQUEST:
            sType = ID_REQUEST_TYPE;
            break;
          case STAY_ALIVE_ACK:
            sType = STAY_ALIVE_ACK_TYPE;
            break;
          case SET_SLEEP_PERIOD_ACK:
            sType = SET_SLEEP_PERIOD_ACK_TYPE;
            break;
          case SET_TARE_ACK:
            sType = SET_TARE_ACK_TYPE;
            break;
          case SET_WIFI_CONFIGURATION_ACK:
            sType = SET_WIFI_CONFIGURATION_ACK_TYPE;
            break;
          case OFFSET_CALIBRATION_ACK:
            sType = OFFSET_CALIBRATION_ACK_TYPE;
            break;
          case SCALE_CALIBRATION_ACK:
            sType = SCALE_CALIBRATION_ACK_TYPE;
            break;
          case SCALE_OUTPUT_MEASURE:
            sType = SCALE_OUTPUT_MEASURE_TYPE;
            break;
          case SCALE_MEASURE_DONE:
            sType = SCALE_MEASURE_DONE_TYPE;
            break;
          case BATTERY_LEVEL_MEASURE:
            sType = BATTERY_LEVEL_MEASURE_TYPE;
            break;
          default:
            translationResult = false;
            break;
       }

       return translationResult;
    }

    void dumpTo(Print& destination) const
    {
        _root.prettyPrintTo(destination);
    }
	
  	size_t messageLength() {
  		  return 	_root.measureLength();
  	}
  
  	void copyTo(char *dest, size_t size)
  	{ 
     		_root.printTo(dest, size); 
  	}
};
