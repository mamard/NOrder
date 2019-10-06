#!usr/bin python
from pyzbar import pyzbar
import cv2

class BarCodeReader:
	@staticmethod
	def readAllFromFile(imgPath):
		return BarCodeReader.decodeAll(imgPath)
		
	@staticmethod
	def readBestFromFile(imgPath):
		bestBarCode = None
		
		barCodesList = BarCodeReader.decodeAll(imgPath)
		if len(barCodesList) > 0:
			barCodesList = sorted(barCodesList, reverse=True, key=lambda bc: bc.rect[2] * bc.rect[3])
			bestBarCode = barCodesList[0].data.decode("utf-8")

		return bestBarCode
		
	@staticmethod
	def decodeAll(imgPath):
		image = cv2.imread(imgPath)
		result = []
		try:
			result =  pyzbar.decode(image)
		except:
			result = []

		return result
