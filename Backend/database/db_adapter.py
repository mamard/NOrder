import psycopg2

from psycopg2 import connect
from psycopg2.extras import RealDictCursor
from psycopg2.sql import SQL, Identifier, Placeholder

DB_USER_LOGIN = "'norder_admin'"
DB_USER_PASSWORD = "'<password>'" #This is (obviously) not the real password ...
DB_NAME = "'norder'"


class BackendDatabaseQueryHandler:
	def __init__(self):
		self.__connectString =  "dbname={} user={} password={}".format(DB_NAME, DB_USER_LOGIN, DB_USER_PASSWORD)

	def __readQueryType(self, queryString):
		return queryString.partition(' ')[0].upper()

	def executeQuery(self, queryString, *args, **kwargs):
		result = {"success": True, "records": None, "error_message": None}
		
		connection = None
		cursor = None
		try:
			connection = connect(self.__connectString)
			cursor = connection.cursor(cursor_factory=RealDictCursor)
		except psycopg2.Error as exc:
			result["error_message"] = "Could not connect to database ..."
			result["success"] = False


		if  cursor is not None:
			#Determine the query type (SELECT, INSERT, UPDATE or DELETE)
			queryType = self.__readQueryType(queryString)

			#Execute the Query
			try:
				queryComposables = []
				fieldIdentifiers = []
							 
				if len(args) > 0 or len(kwargs.items()) > 0:					
					for key, value in kwargs.items():
						fieldIdentifiers.append(value)
					if len(fieldIdentifiers) > 0:
						queryComposables.append(SQL(', ').join(map(Identifier, fieldIdentifiers)))
					for i in range(0, len(args)):
						queryComposables.append(Placeholder())

					placeholder_str = SQL(queryString).format(*queryComposables)
					cursor.execute(placeholder_str, args)
				else:
					cursor.execute(queryString)
			except psycopg2.Error as exc:
				result["success"] = False
				result["error_message"] = "Error while executing SQL query \
				\nPsycopg2 Code: {} \
				\nError Details : {} \
				\nQuery : {} \
				\nParams : {}" \
				.format(exc.pgerror, exc.diag.message_detail, placeholder_str.as_string(connection), *args)

			if (queryType == "SELECT"):
				result["records"] = cursor.fetchall()
			else:
				try:
					connection.commit()
				except psycopg2.Error as exc:
					result["success"] = False
					result["error_message"] = "Error while commiting SQL statement \
					\nPsycopg2 Code: {} \
					\nError Details : {} \
					\nQuery : {} \
					\nParams : {}" \
					.format(exc.pgerror, exc.diag.message_detail, placeholder_str.as_string(connection), *args)

		return result
