#!usr/bin python

from services.optical_recognition.bar_code import BarCodeReader
from services.off.db_access import ProductRequester

from time import sleep
from shutil import copyfile
from socketIO_client import SocketIO, LoggingNamespace
from threading import Thread

import os
import json

import importlib.util

SERVER_USER_UPLOADED_FILES_DIR="/var/www/nordernet.com/server/uploads"
BARCODE_SCANNING_ERROR_FILES_DIR=os.path.join(SERVER_USER_UPLOADED_FILES_DIR, "barcode_scanning_errors")

class Product:
	pass

class NorderWebSocketMessagesHandler:

	def __init__(self, host, port):
		spec = importlib.util.spec_from_file_location("db_proxy", "../database/db_proxy.py")
		db_proxy = importlib.util.module_from_spec(spec)
		spec.loader.exec_module(db_proxy)

		self.__dbServicesProxy = db_proxy.BackendDatabaseServices()

		self.__socketIO = SocketIO(host, port, Namespace=LoggingNamespace, verify=True, wait_for_connection=False)
		self.__socketIO.on('process_product_request_from_off', self.on_off_product_request)
		self.__rcv_event_thread = Thread(target=self.__receive_event_thread)
		self.__rcv_event_thread.daemon = True
		self.__rcv_event_thread.start()

	def __receive_event_thread(self):
		self.__socketIO.wait()

	def on_off_product_request(self, data):
		file_name = data['file_name']
		user_login = data['user_login']
		result={"element_id": data['element_id'], "status": 0, "product_infos": {}, "error_message": ""}
		#Process the bar code optical recognition
		#faFirst check file exits
		image_path = os.path.join(SERVER_USER_UPLOADED_FILES_DIR, file_name)
		if os.path.isfile(image_path):
			bar_code = BarCodeReader.readBestFromFile(image_path)
			productResult={}
			if bar_code is not None:
				#CAUTION : Some products have very poor infos in OFF !!!!!!
				productInfos=ProductRequester.findOneProduct(bar_code,['image_url','brands','product_name','quantity'])
				if productInfos["status"] == 1:				
					product = Product()
					setattr(product, 'bar_code', bar_code)
					setattr(product, 'name', productInfos['product']['product_name'])
					setattr(product, 'brand',  productInfos['product']['brands'])
					setattr(product, 'image_url',  productInfos['product']['image_url'])
					setattr(product, 'quantity',  productInfos['product']['quantity'])

					queryResult = self.__dbServicesProxy.register_product_if_needed(product, user_login)

					if queryResult["success"]:
						result["status"] = 1
						result["product_infos"] = productInfos
					else:
						result["error_message"]= "Erreur lors du référencement du produit dans la base NOrder."
				else:
					result["error_message"]="Le code barre lu ne correspond à aucune entrée dans la base Produits."
			else: #Could not decode a bar code from file
				result["error_message"]="Aucun code barre n'a pu être décodé à partir de l'image."
				error_img_file_path = os.path.join(BARCODE_SCANNING_ERROR_FILES_DIR, file_name)
				copyfile(image_path, error_img_file_path)
		else: #no such existing file
			result["error_message"]="Echec de la lecture de l'image."

		self.emitToServer('product_from_off_response', json.dumps(result, indent=4, ensure_ascii=False))

		if os.path.exists(image_path):
			os.remove(image_path)

	def emitToServer(self, message_name, json_data={}):
		self.__socketIO.emit(message_name,json_data)
			
if __name__ == "__main__":
	wsMessagesHandler = NorderWebSocketMessagesHandler('sbdedemo.ddns.net', 9000)
	while True:
		sleep(1)
