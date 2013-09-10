# ImproveMyCity - Citizens Requests, Complaints & Suggestions

![Screenshots from the application](http://smartcityapps.urenio.org/img/screens_improve_en.png)

ImproveMyCity is a Joomla 2.5.x compatible component to report, vote and track non-emergency issues. 

The application enables citizens to report local problems such as potholes, illegal trash dumping, faulty street lights, broken tiles on sidewalks, and illegal advertising boards. The submitted issues are displayed on the city's map. Users may add photos and comments. Moreover, they can suggest solutions for improving the environment of their neighbourhood. 

ImproveMyCity has been developed within the European project PEOPLE. Find out more at [http://smartcityapps.urenio.org](http://smartcityapps.urenio.org/improve-my-city_en.html).

ImproveMyCity is also available on the [Joomla! Extensions Directory](http://extensions.joomla.org/extensions/clients-a-communities/communities/21164) 

## Installation and Documentation
Refer to ImproveMyCity [wiki pages](https://github.com/icos-urenio/Improve-my-city/wiki).

## Bug Tracker
Have a bug? Please create an issue here on GitHub!
[https://github.com/icos-urenio/Improve-my-city/issues](https://github.com/icos-urenio/Improve-my-city/issues).

## License
ImproveMyCity's source code is licensed under the [GNU Affero General Public License](https://www.gnu.org/licenses/agpl.html).

## Changelog

### Version 2.5.6
* Problem adding new issue when IMC is set on Home menu is fixed

### Version 2.5.5
* Menu parameter HTML5 to select between the old classic layout and the new responsive based on bootstrap and html5
* Include latest bootstrap

### Version 2.5.4
* Major update to support adaptive layout using Bootstrap's row-fluid
* Problem loading map on latest Chrome version is fixed 
* New field to keep track of how issues are inserted (normal/json)

### Version 2.5.3
* REST secure user registration following com_users guidelines completed
* Ready for SSL
* Trash / empty trash issues on administrator is fixed
* Various bootstrap issues fixed
* jQuery and popup modal conflict issues fixed
* Ordering issue occuring in some templates is fixed

### Version 2.5.2
* Fixing upload photo server-path when adding new issue from mobile.json
* Added getUserVotes() in mobile.json

### Version 2.5.1 security update
* Security update concerning encryption/decryption. The method is now Android compatible
* Secret key is now stored on DB instead of component's parameters
* Administrator various fixes and submenus added
* Updated mobile.json controller according to this [snippet](http://www.androidsnippets.com/encrypt-decrypt-between-android-and-php)

### Version 2.5.0 
Brings complete support for mobile versions (Android, iOS, REST services) of ImproveMyCity, for third parties, by introducing a complete json wrapper. All gathered together through a common controller interface which administrators can activate/deactivate on demand.

Mobile json wrapping supports:

- Encryption / Decryption functionality with secret key defined on server side
- Timestamp on DB changes
- Introduces geo-boundaries queries
 
Also v2.5.0 brings the following:
- New settings for displaying relative dates or plain dates with custom date format
- New settings to enable/disable json support for the new mobile-oriented controller
- Secret key on settings to be used for password encryption over http (It is highly recommended to use HTTPS)
- Remove login link on add new issue and add new comment
- Mega-menu css enhancements
- Fix administration approval issues (you can set on settings if you want to publish directly new issues or the administrator must first approve)

### Version 2.4.0 
* Just a numbering convension to 3 level versioning

### Version 2.3 
* Fixes the conflict between mootools/jquery which caused megamenu or/and google map not to work when certain templates are used.

### Version 2.2.1 
* Introduces automatic updates and also improves comments administration and fixes dropdown categories bug concerning unpublished items

### Version 2.2 
* Introduces comments administration and suggestions and bug fixes based on JED and support group users. Also, new settings are added like: Show/Hide comments, Publish new issue on admin approval and more. Settings are now fully translated. Comment submit button is disabled during ajax call to avoid duplicate comments.

### Version 2.1 
* Fixes some ACL features

### Version 2.0
* Contains many additions and bug fixes based on end-users comments and testing phase feedback






 

