#!/usr/bin python

import json
import threading 

from time import sleep

from socketserver import ThreadingTCPServer
from socketserver import BaseRequestHandler

#Define the type of the messages sent to eRecipients
ID_RESPONSE_TYPE = "ID_RESPONSE"
SET_WIFI_CONFIG_TYPE = "SET_WIFI_CONFIG_TYPE"
OFFSET_CALIBRATION_TYPE = "OFFSET_CALIBRATION"
SCALE_CALIBRATION_TYPE = "SCALE_CALIBRATION"
SET_TARE_TYPE = "SET_TARE"

#Define the type of messages received from eRecipients
ID_REQUEST_TYPE = "ID_REQUEST"
SET_WIFI_CONFIG_TYPE_ACK = "SET_WIFI_CONFIG_TYPE_ACK"
OFFSET_CALIBRATION_ACK_TYPE = "OFFSET_CALIBRATION_ACK"
SCALE_CALIBRATION_ACK_TYPE = "SCALE_CALIBRATION_ACK"
SET_TARE_ACK_TYPE = "SET_TARE_ACK"

MAX_BUFFER_SIZE = 1024				

class eContainerTCPHandler(BaseRequestHandler):
	#def __init__(self):
		#super(eContainerTCPHandler,self).__init__()
		#self.__actionMap = {}
		#self.__actionMap[ID_REQUEST_TYPE] = self.__parseIdRequestMsg
		#self.__actionMap[SCALE_MEASURE_TYPE] = self.__parseScaleMeasureMsg
		#self.__actionMap[TARE_SET_ACK_TYPE] = self.__parseTareSetAckMsg
		#self.__actionMap[SLEEP_PERIOD_ACK_TYPE] = self.__parseSleepPeriodSetAckMsg

	def handle(self):
		# self.request is the TCP socket connected to the client
		rcvMessage = self.request.recv(MAX_BUFFER_SIZE).strip()
		print("Message received : {}".format(rcvMessage))

		idResponseMsg = self.__makeResponse(ID_RESPONSE_TYPE, {'Status': 1})
		self.request.send(str.encode(json.dumps(idResponseMsg, indent=4)))

		input("Press Enter when ready to start the calibration process ....")

		offsetCalibrationMsg = self.__makeResponse(OFFSET_CALIBRATION_TYPE)
		self.request.send(str.encode(json.dumps(offsetCalibrationMsg, indent=4)))

		rcvMessage = self.request.recv(MAX_BUFFER_SIZE).strip()
		print("Message received : {}".format(rcvMessage))

		while True:
			if (self.__readMessageType(rcvMessage) == OFFSET_CALIBRATION_ACK_TYPE): 
				break
			sleep(1)
			
		referenceMass = input("Enter reference mass value for scale calibration : ")

		scaleCalibrationMsg = self.__makeResponse(SCALE_CALIBRATION_TYPE, {'ReferenceMass': referenceMass})
		self.request.send(str.encode(json.dumps(scaleCalibrationMsg, indent=4)))

		rcvMessage = self.request.recv(MAX_BUFFER_SIZE).strip()
		print("Message received : {}".format(rcvMessage))

		while True:
			if (self.__readMessageType(rcvMessage) == SCALE_CALIBRATION_ACK_TYPE): 
				break
			sleep(1)

		input("Now put the glass jar on the eBase and press Enter when ready to setup the tare :")

		setTareMsg = self.__makeResponse(SET_TARE_TYPE)
		self.request.send(str.encode(json.dumps(setTareMsg, indent=4)))

		rcvMessage = self.request.recv(MAX_BUFFER_SIZE).strip()
		print("Message received : {}".format(rcvMessage))

		while True:
			if (self.__readMessageType(rcvMessage) == SET_TARE_ACK_TYPE): 
				break
			sleep(1)

		print("Calibration process completed.")
		

		#while hasMoreThingsToTell:
			
			#try:
			#except(ValueError):
				#pass
	def __makeResponse(self, msgType, msgParams={}):
		responseObject = {}
		responseObject["TYPE"] = msgType

		if len(msgParams.items()) > 0:
			responseObject["PARAMS"] = {}
			for k,v in msgParams.items():
				responseObject["PARAMS"][k] = v

		return responseObject


	
	def __readMessageType(self, msg):
		msgType=None
		msgObject={}
		try:
			msgObject = json.loads(msg)	
		except ValueError:
			pass


		try:
			msgType = msgObject["TYPE"]
		except KeyError:
			pass

		return msgType
		
class eContainerCommHandler:
	def __init__(self, host, port):
		self.__tcpServer = ThreadingTCPServer((host, port), eContainerTCPHandler)
		
	def start(self):
		print("Calibration TCP Server now listening on port {}".format(11501))
		self.__tcpServer.serve_forever()
		
if __name__ == "__main__":
	commHandler = eContainerCommHandler("192.168.1.29" , 11501)
	commHandler.start()
