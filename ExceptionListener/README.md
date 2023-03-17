# Phalconeer\Exception

The idea behind this repository is to showcase some advanced handling of Exceptions, extending the default PHP ones.

# Exception codes

It is advised to follow a strict format for the numeric exception codes from the beggining.
Exception numbers should follow the following format
AMMZZYYXXX, where
* A is the code for the application it starts with 1 assigned to Common (Fantasee layer). Phalconeer layer does not use this digit.
* MM is the 2 digit module code from this file, starting form 10 up to 99. Non-module exception does not use these digits (or have them as 00 if they are application specific)
* ZZ is the class identifier within the module / generic context. It starts with 01 and goes up to 99. It is up to the context to decide which class gets which number.
* YY is the 2 digit code identifying the generic type of error
    * System errors
        * 00 - invalid argument
        * 01 - route not found
        * 02 - file not found
        * 03 - class not found
        * 04 - module not found
        * 05 - external service error
        * 06 - configuration error
    * Authentication related
        * 10 - guest / application (invalid tokens)
        * 11 - user (missing credentials, user id mismatch)
        * 12 - scope (missing, invalid etc.)
    * Validation
        * 20 - generic request header errors
        * 21 - generic request body errors (formatting, etc.)
        * 22+ - business validation
    * Payment
        * 30 - any kind of payment related exception

## Module codes for Phalconeer
* 10 - Application
* 11 - MySqlAdapter
* 12 - Dao
* 13 - Memcache
* 14 - ElasticAdapter
* 15 - Auth
* 16 - User
* 17 - ?LoginApplication
* 18 - ?AuthenticateBearer
* 19 - ?LoginCredentials
* 20 - ?LoginSession
* 21 - ?FormValidator
* 22 - ?RestRequest
* 23 - ?RestResponse
* 24 - View
* 25 - ?ViewSimple
* 26 - ?Router
* 27 - ?Condition
* 28 - ?Loader
* 29 - Data
* 30 - Dto
* 31 - ?Middleware
* 32 - ?CurlClient
* 33 - ?Browser
* 34 - ?MailgunAdapter
* 35 - ?XmlDocumentParser
* 36 - ?TemplateSvelte
* 37 - ?Admin
* 38 - LiveSession
* 39 - AuthAdmin
* 40 - Impression
* 41 - ?Task
* 42 - UserAdmin
* 43 - ?SoapClient
* 44 - ?
* 45 - ?
* 46 - ?
* 47 - ?GoogleApiAuth
* 48 - ?AuthenticateDeviceId
* 49 - ?
* 50 - ?
* 51 - ?
* 52 - ?