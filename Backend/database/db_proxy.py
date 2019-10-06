import importlib.util

import os
db_adapter_module_path = os.path.join(os.path.dirname(os.path.realpath(__file__)), "db_adapter.py")

spec = importlib.util.spec_from_file_location("db_adapter", db_adapter_module_path)
db_adapter = importlib.util.module_from_spec(spec)
spec.loader.exec_module(db_adapter)

#from db_adapter import BackendDatabaseQueryHandler

CHECK_IF_ERICIPENT_IS_REFERENCED_QUERY = "SELECT count(*) AS is_referenced FROM referenced_mac_adresses WHERE id = {}"

CHECK_IF_PRODUCT_REFERENCED = "SELECT DISTINCT count(pabu.id) as user_product_referenced \
FROM products  p  \
FULL OUTER JOIN users u ON  u.login = {} \
FULL OUTER JOIN product_added_by_user pabu ON p.id = pabu.product_id and u.id = pabu.user_id \
WHERE p.bar_code = {} \
GROUP BY(p.id, pabu.id)"

REGISTER_PRODUCT_QUERY = "INSERT INTO products VALUES(DEFAULT, {},{},{},{},{})"

REGISTER_USER_PRODUCT_ADDITION_QUERY = "INSERT INTO product_added_by_user \
VALUES(DEFAULT,(SELECT id FROM users WHERE login = {}), (SELECT id FROM products WHERE bar_code = {}), now())"

REGISTER_ERECIPIENT_QUERY = "INSERT INTO erecipients VALUES(DEFAULT, {},{})"

REGISTER_ERECIPIENT_CONNECTION_TIME = "UPDATE erecipients SET last_connection_date = TO_TIMESTAMP({}, 'DD/MM/YYYY HH24:MI:SS.US') WHERE mac_address = {}"

UPDATE_ERECIPIENT_BATTERY_LEVEL = "UPDATE erecipients SET battery_level = {} WHERE mac_address = {}"

UPDATE_PRODUCT_ITEM_STORE_VALUE = "UPDATE product_item_store AS pis \
SET   quantity = {} \
FROM  erecipient_product_binding epb, erecipients e \
WHERE pis.erecipient_product_binding_id = epb.id \
AND epb.erecipient_id = e.id \
AND e.mac_address = {}"

GET_ERECIPIENT_INFOS = "SELECT {} FROM erecipients WHERE mac_address = {}"

GET_USER_ERECIPIENT_PREFERENCES = "SELECT {} FROM user_erecipient_preferences uep \
INNER JOIN erecipient_preference_parameter epp ON uep.parameter_id = epp.id \
INNER JOIN users u ON uep.user_id = u.id \
INNER JOIN erecipients e ON u.id = e.owner_id AND uep.erecipient_id = e.id \
WHERE e.mac_address = {}"

IS_RECIPIENT_BOUND_TO_PRODUCT = "SELECT count(epb.id) AS is_bound FROM erecipient_product_binding epb INNER JOIN erecipients e ON epb.erecipient_id = e.id WHERE e.mac_address = {}"

INSERT_NEW_ITEM_STORE = "INSERT INTO product_item_store VALUES (DEFAULT, (SELECT id FROM erecipient_product_binding WHERE ), -1, 4)"
REUSE_ITEM_STORE = "UPDATE product_item_store SET erecipient_product_binding = {}"

class BackendDatabaseServices:
	def __init__(self):
		self.__queryHandler = db_adapter.BackendDatabaseQueryHandler()
			
	def check_if_erecipient_is_referenced(self, macAdress):
		queryResult = self.__queryHandler.executeQuery(CHECK_IF_ERICIPENT_IS_REFERENCED_QUERY, macAdress)
		
		if not queryResult["success"]:
			raise StandardError("Error while executing query")

		record = queryResult["records"][0]
		
		return record["is_referenced"] == 1

	def check_if_product_referenced(self, product_bar_code, user_login):
		queryResult = self.__queryHandler.executeQuery(CHECK_IF_PRODUCT_REFERENCED, user_login, product_bar_code)
		return queryResult

	def register_product_if_needed(self, product, userLogin):
		queryResult = self.check_if_product_referenced(product.bar_code, userLogin)
		if queryResult["success"]:
			if len(queryResult["records"]) == 0: #new product must be inserted in Norder database
				product_fields = [product.bar_code,product.name,product.brand,product.image_url,product.quantity]
				queryResult = self.register_product(*product_fields)
				if queryResult["success"]:
					queryResult = self.register_user_product_addition(userLogin, product.bar_code)

			else:
				if queryResult["records"][0]["user_product_referenced"] == 0:
					queryResult = self.register_user_product_addition(userLogin, product.bar_code)
							
		return queryResult

	def register_product(self, *fieldValues):
		queryResult = self.__queryHandler.executeQuery(REGISTER_PRODUCT_QUERY, *fieldValues)
		return queryResult

	def register_user_product_addition(self, userLogin, productBarCode):
		queryResult = self.__queryHandler.executeQuery(REGISTER_USER_PRODUCT_ADDITION_QUERY, userLogin, productBarCode)
		return queryResult

	def register_new_erecipient(self, macAdress, **kwargs):
		pass

	def register_erecipient_connection_time(self, macAdress, connectionTime):
		queryResult = self.__queryHandler.executeQuery(REGISTER_ERECIPIENT_CONNECTION_TIME, connectionTime, macAdress)
		return queryResult

	def is_erecipient_bound_to_product(self, macAdress):
		queryResult = self.__queryHandler.executeQuery(IS_RECIPIENT_BOUND_TO_PRODUCT, macAdress)
		
		if not queryResult["success"]:
			return False

		record = queryResult["records"][0]
		
		return record["is_bound"] == 1
	
	def update_product_item_store_value(self, macAdress, newQty):
		queryResult = self.__queryHandler.executeQuery(UPDATE_PRODUCT_ITEM_STORE_VALUE, newQty, macAdress)
		return queryResult

	def update_erecipient_battery_level(self, macAdress, newLevel):
		queryResult = self.__queryHandler.executeQuery(UPDATE_ERECIPIENT_BATTERY_LEVEL, newLevel, macAdress)
		return queryResult

	def get_recipient_infos(self, macAdress, requestedFields):
		fieldsRequired = {}

		nField = 0
		for fieldName in requestedFields:
			nField += 1
			fieldsRequired["field_{}".format(nField)] = fieldName

		queryResult = self.__queryHandler.executeQuery(GET_ERECIPIENT_INFOS, macAdress, **fieldsRequired)
		return queryResult
	
	def get_user_erecipient_preferences(self, macAdress, *requestedParameters):
		fieldsRequired = {"field_1": "name", "field_2": "parameter_value"}

		queryResult = self.__queryHandler.executeQuery(GET_USER_ERECIPIENT_PREFERENCES, macAdress, **fieldsRequired)
		queryResult["records"] = list(filter(lambda record: record["name"] in requestedParameters, queryResult["records"]))

		return queryResult
		
	
