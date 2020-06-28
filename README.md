## User-Manager
if you are looking for a simple user manager system, so you can use it in your own app. this is for you.
[![youtube](https://raw.githubusercontent.com/irhosseinz/User-Manager/master/install/screen_shots/youtube.jpg)](https://www.youtube.com/watch?v=5W0f3uuUAd0)

## Fields Customization
You can add as many extra data fields for user, based on your project. for each field these properties are Customizable:
* __Field Name__
* __Field Type__: you can select these types for each field (all types are validated while entering data by user using [jQuery Validation](https://jqueryvalidation.org/))
	* Text
	* Number
	* Date
	* Url
	* Email
	* Select Options: you can set a series of options that user can select one of them by html select field
	* Checkbox
* __Regex Validation__: An Extra regex validation can be set too check that data is valid
* __Don't get data in Registeration__: You can set to get data in registration or not, if you check this that field won't exist in registeration and user can enter it after registeration in his/her profile
* __Unique__: If you check this that field would be unique in database and also there will be a extra [remote validation](https://jqueryvalidation.org/remote-method/) for this field while entering data
* __UnEditable__: If you check this that field would be Uneditable after registeration
* __Required__: User is forced to fill this field
* __Min Length__
* __Max Length__
* __Hint__: You can enter a hint about that field to be shown in forms


## Main Features
* __Administrator__: There is an Administrator panel with these capabilities
	* View All Users and Their Data
	* Reset a User's password
	* Grant Administrator Permissions to other users
* __Fully Customizable__: There is an installation script that you can configure Website Title, domain, email(that is used while sending emails) AND user properties in the way you want
![install](https://raw.githubusercontent.com/irhosseinz/User-Manager/master/install/screen_shots/install.png)
![install_fields](https://raw.githubusercontent.com/irhosseinz/User-Manager/master/install/screen_shots/install_fields.png)
* __Registration__: Simple Registeration With jquery form Validation
![register](https://raw.githubusercontent.com/irhosseinz/User-Manager/master/install/screen_shots/register.png)
* __Login__: User can login and its data is saved in __$_SESSION['UM_DATA']__. you can use it anywhere you want!
	* There is __Remember Me__ option in login. user can select it so he will stay online for 10 days (you can change this by changing __UM_LOGIN_EXPIRE__ in __config.php__)
* __Dashboard__: dashboard contains a profile manager that user can edit his profile data, But you can add other sections to it. I've used [Feather Icons](https://feathericons.com/) for icons. you can use them easily
![dashboard](https://raw.githubusercontent.com/irhosseinz/User-Manager/master/install/screen_shots/dashboard.png)
* __Email Verification__
* __Password Reset__: User can reset his/her password in case of forgeting that
* __Secure__: All security measures are observed
* __Captcha Support__: A Simple Captcha Code Could be Activated to Be used for Login And Registeration.
* __Recaptcha Support__: Additionally You can get [Recaptcha V3](https://www.google.com/recaptcha/admin) api Keys and enter it while Installation, so recaptcha will be used in _background_ on login. (Google Recaptcha V3 is not For Actual Bot detection. It could be used if You don't want captcha but still want to know that someone trying to do some automation on your website.) 
* __Referral System__: There is an optional referral system that could be activated on installation. using that users can get a referral link and introduce your system to others, you can view its data in admin panel and you can use it for other cases by accessing `ref` field in `users` table in database
* __Google Authenticator__: There is an optional [google authenticator](https://en.wikipedia.org/wiki/Google_Authenticator) support that could be activated on installation. using that users can activate two factor verification in their account for more security.

## Special Thanks
  [@thibaut-decherit](https://github.com/thibaut-decherit) - For security notes

## Show your support
Give a ⭐️ if this project helped you!

❤️Donation -> Bitcoin:179CsAFEucLbQG6WDLTxVRX2ax8NBrxcGU
