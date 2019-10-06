#!/usr/bin python
import os
import json

from time import sleep
from datetime import datetime

from socketserver import ThreadingTCPServer
from socketserver import BaseRequestHandler

import importlib.util

#Define the type of the messages sent to eRecipients
ID_RESPONSE_TYPE = "ID_RESPONSE"
STAY_ALIVE_TYPE = "STAY_ALIVE"
SET_SLEEP_PERIOD_TYPE = "SET_SLEEP_PERIOD"
SET_TARE_TYPE = "SET_TARE" 
SCALE_QUERY_TYPE = "SCALE_QUERY"
BATTERY_LEVEL_QUERY_TYPE = "BATTERY_LEVEL_QUERY"

#Define the type of messages received from eRecipients
ID_REQUEST_TYPE = "ID_REQUEST"
STAY_ALIVE_ACK_TYPE = "STAY_ALIVE_ACK"
SCALE_OUTPUT_MEASURE_TYPE = "SCALE_OUTPUT_MEASURE"
SCALE_MEASURE_DONE_TYPE = "SCALE_MEASURE_DONE"
SET_TARE_ACK_TYPE = "SET_TARE_ACK"
SET_SLEEP_PERIOD_ACK_TYPE = "SET_SLEEP_PERIOD_ACK"
BATTERY_LEVEL_MEASURE_TYPE="BATTERY_LEVEL_MEASURE"

MAX_BUFFER_SIZE = 1024	

class ErecipEvent(object):
    def __init__(self):
    	pass

class Observable(object):
    def __init__(self):
        self.callbacks = []
    def subscribe(self, callback):
        self.callbacks.append(callback)
    def fire(self, **attrs):
        e = ErecipEvent()
        e.source = self
        for k, v in attrs.iteritems(): 
            setattr(e, k, v)
        for fn in self.callbacks:
            fn(e)			

class eRecipientTCPHandler(BaseRequestHandler):
	def __init__(self, request, client_address, server):
		
		self.__actionMap = {}
		self.__actionMap[ID_REQUEST_TYPE] = self.__handleIdRequestMsg
		self.__actionMap[SCALE_OUTPUT_MEASURE_TYPE] = self.__handleScaleOutputMeasureMsg
		self.__actionMap[SCALE_MEASURE_DONE_TYPE] = self.__handleScaleMeasureDoneMsg
		self.__actionMap[SET_TARE_ACK_TYPE] = self.__handleSetTareAckMsg
		self.__actionMap[SET_SLEEP_PERIOD_ACK_TYPE] = self.__handleSetSleepPeriodAckMsg
		self.__actionMap[STAY_ALIVE_ACK_TYPE] = self.__handleStayAliveAckMsg
		self.__actionMap[BATTERY_LEVEL_MEASURE_TYPE] = self.__handleBatteryLevelMeasureMsg

		self.__connectedMacAddress = ''
		
		self.__messagesSequenceSteps = []

		spec = importlib.util.spec_from_file_location("db_proxy", "../database/db_proxy.py")
		db_proxy = importlib.util.module_from_spec(spec)
		spec.loader.exec_module(db_proxy)
		self.__servicesProvider = db_proxy.BackendDatabaseServices()

		BaseRequestHandler.__init__(self, request,client_address,server)

	def handle(self):
		self.request.settimeout(10)
		# self.request is the TCP socket connected to the client
		rcvMessage = self.request.recv(MAX_BUFFER_SIZE).strip()
		print("Message received : {}".format(rcvMessage))

		macAddressReferenced = False
		idResponseStatus = 1

		parseResult = self.__parseMessage(rcvMessage)

		if parseResult["Type"] == ID_REQUEST_TYPE:
			try:
				macAddressReferenced = self.__handleIdRequestMsg(parseResult["MessageObject"])
				if not macAddressReferenced:
					idResponseStatus = 0
				else:
					idResponseStatus = 1
			except:
				return

			idResponseMessage = self.__makeMessage(ID_RESPONSE_TYPE, {'Status': idResponseStatus})
			self.request.send(str.encode(json.dumps(idResponseMessage, indent=4)))

		sleep(1)

		if macAddressReferenced:
			print("Identification of MAC adress '{}' OK".format(self.__connectedMacAddress))
			
			self.__prepareMessagesSequence(self.__connectedMacAddress)
			print("Starting message exchange sequence with {} ...".format(self.__connectedMacAddress))

			exitMessageSequenceLoop = False 
			for seqStep in self.__messagesSequenceSteps:
				message = seqStep["message_to_send"]
				delay = seqStep["delay_to_wait_before_response"]

				if message is not None:
					print("Sending  message '{}' to '{}'".format(message, self.__connectedMacAddress))
					self.request.send(str.encode(json.dumps(message, indent=4)))

				sleep(delay)

				try:
					print("Waiting '{}' response from '{}'".format(seqStep["expected_response_type"], self.__connectedMacAddress))
					while True:
						rcvMessage = self.request.recv(MAX_BUFFER_SIZE).strip()
						print("Message received from '{}' : {}".format(self.__connectedMacAddress, rcvMessage))

						parseResult = self.__parseMessage(rcvMessage)
						if (parseResult["Type"] == seqStep["expected_response_type"] or seqStep["expected_response_type"] == SET_SLEEP_PERIOD_ACK_TYPE): 
							if not self.handleMessage(parseResult["Type"], parseResult["MessageObject"]):
								print("Error while handling message received by {}".format(self.__connectedMacAddress))
							break
						sleep(1)
				except Exception as e:
					print(e.message)
					exitMessageSequenceLoop = True
				finally:
					pass

				if exitMessageSequenceLoop:
					break

			print("End of message exchange sequence with '{}'. Closing connection ...".format(self.__connectedMacAddress))

	def __prepareMessagesSequence(self, macAdress):
		#Define the sequence of messages to send to the connected eRecipient
		#SET_SLEEP_PERIOD
		#STAY_ALIVE
		# if there is a store -> SCALE_QUERY (wait for  & )
		#BATTERY_LEVEL_QUERY

		#First retrieve the sleeping period (s)
		queryResult = self.__servicesProvider.get_user_erecipient_preferences(macAdress, "erecipient_awakening_period")

		if queryResult["success"] and len(queryResult["records"]) > 0:
			awakeningPeriod = int(queryResult["records"][0]["parameter_value"])
			self.__messagesSequenceSteps.append( \
			{"message_to_send": self.__makeMessage(SET_SLEEP_PERIOD_TYPE, {'Period': awakeningPeriod}), \
			"delay_to_wait_before_response": 1, \
			"expected_response_type": SET_SLEEP_PERIOD_ACK_TYPE} \
			)
		else:
			pass

		self.__messagesSequenceSteps.append( \
		{"message_to_send": self.__makeMessage(STAY_ALIVE_TYPE, {'Period': 300}), \
		"delay_to_wait_before_response": 1, \
		"expected_response_type": STAY_ALIVE_ACK_TYPE} \
		)

		if self.__servicesProvider.is_erecipient_bound_to_product(macAdress):
			self.__messagesSequenceSteps.append( \
			{"message_to_send": self.__makeMessage(SCALE_QUERY_TYPE, {"Slot": 1}), \
			"delay_to_wait_before_response": 1, \
			"expected_response_type": SCALE_OUTPUT_MEASURE_TYPE} \
			)
			self.__messagesSequenceSteps.append( \
			{"message_to_send": None, \
			"delay_to_wait_before_response": 3, \
			"expected_response_type": SCALE_MEASURE_DONE_TYPE} \
			)

		self.__messagesSequenceSteps.append( \
		{"message_to_send": self.__makeMessage(BATTERY_LEVEL_QUERY_TYPE), \
		"delay_to_wait_before_response": 1, \
		"expected_response_type": BATTERY_LEVEL_MEASURE_TYPE} \
		)

	def __makeMessage(self, msgType, msgParams={}):
		messageObject = {}
		messageObject["TYPE"] = msgType

		if len(msgParams.items()) > 0:
			messageObject["PARAMS"] = {}
			for k,v in msgParams.items():
				messageObject["PARAMS"][k] = v

		return messageObject

	def __handleIdRequestMsg(self, msg):
		result = True
		macAdress = ""
		
		try:
			params = msg["PARAMS"]
			macAdress = params["MacAdress"]
		except:
			raise KeyError("")

		checkQueryResult = self.__servicesProvider.check_if_erecipient_is_referenced(macAdress)
		if checkQueryResult:
			self.__connectedMacAddress = macAdress
			self.__servicesProvider.register_erecipient_connection_time(macAdress, datetime.now().strftime('%d/%m/%Y %H:%M:%S.%f'))
			result = True
		else:
			result = False

		return result

	def __handleScaleOutputMeasureMsg(self, msg):
		try:
			params = msg["PARAMS"]
			massValue = params["Mass"]

			(quotient, remainder) = (massValue // 10, massValue % 10)

			if remainder > 5:
				massValue = quotient * 10 + 10;
			else:
				massValue = quotient * 10

		except:
			raise KeyError("")

		return self.__servicesProvider.update_product_item_store_value(self.__connectedMacAddress, massValue)

	def __handleScaleMeasureDoneMsg(self, msg):
		return True

	def __handleSetTareAckMsg(self, msg):
		return True

	def __handleSetSleepPeriodAckMsg(self, msg):
		return True

	def __handleStayAliveAckMsg(self, msg):
		return True

	def __handleBatteryLevelMeasureMsg(self, msg):
		try:
			params = msg["PARAMS"]
			levelValue = params["Level"]
		except:
			raise KeyError("")

		return self.__servicesProvider.update_erecipient_battery_level(self.__connectedMacAddress, levelValue)

	def __parseMessage(self, msg):
		parseResult = {"Type": None, "MessageObject": None}
		msgType=None
		msgObject=None

		try:
			msgObject = json.loads(msg)	
		except ValueError:
			msgObject = None

		if msgObject is not None:
			try:
				msgType = msgObject["TYPE"]
				parseResult["Type"] = msgType
				parseResult["MessageObject"] = msgObject
			except KeyError:
				pass

		return parseResult

	def handleMessage(self, messageType, jsonObject):
		result = False
		if messageType is not None:
			try:
				result = self.__actionMap[messageType](jsonObject)
			except KeyError:
				result = False
	 
		return result	
		
class eRecipientCommHandler(Observable):
	def __init__(self, host, port):
		self.__tcpServer = ThreadingTCPServer((host, port), eRecipientTCPHandler)
		Observable.__init__(self)

	def add_observer(self, callback):
		self.observers.append(callback)

	def notify_(sef, jsonMessage):
		e = ErecipEvent()
		e.source = self

		setattr(e, message, jsonMessage)

		for callback in self.observers:
			callback(e)

		
	def start(self):
		print("TCP Server now listening on port {}".format(11500))
		self.__tcpServer.serve_forever()
		
if __name__ == "__main__":
	ipAddress = os.popen("hostname -I").read()
	ipAddress = ipAddress.replace(" ", "")
	ipAddress = ipAddress.replace("\n", "")
	
	commHandler = eRecipientCommHandler(ipAddress , 11500)
	commHandler.start()
