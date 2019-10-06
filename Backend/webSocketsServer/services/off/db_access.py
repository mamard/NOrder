#!usr/bin python
#coding: utf-8

import json

import openfoodfacts

class ProductRequester:
	
	@staticmethod
	def findOneProduct(productId, fieldFilters=None):
		res_map = {"status": 0, "product": {}}
		product = openfoodfacts.products.get_product(productId)

		if product['status'] == 1:
			res_map["status"] = 1
			res_map['product']={}
			if fieldFilters is not None:	
				for filter in fieldFilters:
					try:
						fieldValue=product['product']
						for field in filter.split('/'):
							fieldValue=fieldValue[field]
						res_map['product'][filter]=fieldValue
					except:
						res_map['product'][filter]=""
			else:
				res_map['product']=product['product']
		
		#json_res = json.dumps(res_map, ensure_ascii=False)

		return res_map
