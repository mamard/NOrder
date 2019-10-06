#include <ArduinoJson.h>

#include "BackendMessageDefs.h"

class JsonResponseParser {
	DynamicJsonBuffer _buffer;
public:
	JsonResponseParser(){
		
	}

	~JsonResponseParser(){
		_buffer.clear();
	}

	backend_message_type resolveMessageType(const char* typeFieldValue) {

		int len = strlen(typeFieldValue); 

		if (strncmp(typeFieldValue, ID_RESPONSE_TYPE, len) == 0) {
			return ID_RESPONSE;
		}
    if (strncmp(typeFieldValue, STAY_ALIVE_TYPE, len) == 0) {
      return STAY_ALIVE;
    }
		else if (strncmp(typeFieldValue, SET_SLEEP_PERIOD_TYPE, len) == 0) {
			return SET_SLEEP_PERIOD;
		}
		else if (strncmp(typeFieldValue, SET_TARE_TYPE, len) == 0) {
			return SET_TARE;
		}
    else if (strncmp(typeFieldValue, SET_WIFI_CONFIGURATION_TYPE, len) == 0) {
      return SET_WIFI_CONFIGURATION;
    }
    else if (strncmp(typeFieldValue, OFFSET_CALIBRATION_TYPE, len) == 0) {
      return OFFSET_CALIBRATION;
    }
    else if (strncmp(typeFieldValue, SCALE_CALIBRATION_TYPE, len) == 0) {
      return SCALE_CALIBRATION;
    }
		else if (strncmp(typeFieldValue, SCALE_QUERY_TYPE, len) == 0) {
			return SCALE_QUERY;
		}
    else if (strncmp(typeFieldValue, BATTERY_LEVEL_QUERY_TYPE, len) == 0) {
      return BATTERY_LEVEL_QUERY;
    }
    else if (strncmp(typeFieldValue, GO_TO_SLEEP_TYPE, len) == 0) {
      return GO_TO_SLEEP;
    }
		else
			return UNKNOWN_TYPE;
	}

	bool parseMessage(const char* rcvMessage, backend_message& message){
		bool parseOk = true;

		int msgLength = strlen(rcvMessage);

		JsonObject& root = _buffer.parseObject(rcvMessage);

		parseOk &= root.success();

		if (parseOk) {
			const char* typeField = root["TYPE"];
      int nMainFields = root.size();
      
			if (parseOk &= (nMainFields <= 2 && NULL != typeField)){
				backend_message_type eType = resolveMessageType(typeField);
				parseOk &= (eType != UNKNOWN_TYPE);
				if (parseOk) {
					message.type = eType;

          if (root.containsKey("PARAMS")){
            JsonObject& paramsObject = root["PARAMS"].as<JsonObject>();
            
            for (auto kv : paramsObject){
               
               if (kv.value.as<JsonObject>().size() > 0){
                   Serial.println("[JsonResponseParser::parseMessage] Error while parsing parameter : value must be atomic !");
                   continue;
               }

               message.addParam(String(kv.key), kv.value);
            }
				  }
			  }
		  }
		  else{
        Serial.println("[JsonResponseParser::parseMessage] Error : bad message format");
		  }
		}
		
		return parseOk;
	}
};
