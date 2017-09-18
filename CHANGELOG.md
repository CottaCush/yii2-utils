# Changelog

#### 2.0.1
* Rename flash messages JavaScript variable in BaseController *2017-09-18*


#### 2.0.0

* Remove `beforeAction` and create `loginRequiredBeforeAction` in BaseController *2017-07-06*
* Add `redirectUrl` to `returnError` and `returnSuccess` methods in BaseController *2017-07-06*
* Fix redirect issue on render widget as ajax

#### 1.16.0

* Allow redirect to URL if request is not ajax in `CottaCush\Yii2\Controller:renderWidgetAsAjax`

#### 1.15.1

* Fix issue with getActive and findActive in BaseModel


#### 1.15.0

* Fix issue with filterParams method in TerraHttpClient to allow object and arrays *2017-06-14*
* Add Update Action *2017-06-14*


#### 1.14.0

* Add Save Action *2017-06-14*


#### 1.13.0

* Add data paginator active provider *2017-06-14*

#### 1.12.0

* Add DeleteAction *2017-06-13*


#### 1.11.0

* Add more utility methods to BaseController *2017-06-12*

#### 1.10.0

* Add is POST method check to BaseController *2017-06-09*


#### 1.9.0

* Add base console controller *2017-06-08*

#### 1.8.0

* Add base controller, model and text utils *2017-06-05*

#### 1.7.0

* Support PHP version 7 *2017-05-24*

#### 1.6.0

* Added `getAccessToken()` *2017-05-05*

#### 1.5.0

* Upgraded the lislin cURL dependencies *2017-05-05*

#### 1.4.0

* Fetch Access Token with Client Credentials Grant Type *2017-03-27*


#### 1.3.0

* Add JSendResponse for handling JSend formatted API responses *2016-10-12*


#### 1.2.0

* Add Oauth2Client for getting Oauth2 tokens *2016-09-19*


#### 1.1.0
* Add Date Utils for date formatting and processing *2016-09-16*

#### 1.0.1

* Bug Fix: Sending post request with json body and oauth is enabled throws "Illegal string offset 'access_token'" *2016-07-21*

#### 1.0.0

* Add HttpClient *2016-07-20*