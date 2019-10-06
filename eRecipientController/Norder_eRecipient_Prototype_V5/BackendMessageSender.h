#include "JsonMessageBuilder.h"

#include <WiFi.h>

class BackendMessageSender 
{
	WiFiClient * _tcpClient = NULL;
	
public: 
	BackendMessageSender(WiFiClient * tcpClient) : _tcpClient(tcpClient) {};
	void writeToServer(const backend_message& message)
	{
    JsonMessageBuilder messageBuilder;

    if (messageBuilder.process(message)) {
        int msgSize = messageBuilder.messageLength() + 1;
        
        char jsonMessage[msgSize];    
        messageBuilder.copyTo(jsonMessage, msgSize);

        Serial.printf("Sending %s to backend\n",jsonMessage);
        
        _tcpClient->write(jsonMessage);
        _tcpClient->flush();
    }
	}
};
