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

### Version 2.0
* Contains many additions and bug fixes based on end-users comments and testing phase feedback

### Version 2.1 
* Fixes some ACL features

### Version 2.2 
* Introduces comments administration and suggestions and bug fixes based on JED and support group users. Also, new settings are added like: Show/Hide comments, Publish new issue on admin approval and more. Settings are now fully translated. Comment submit button is disabled during ajax call to avoid duplicate comments.

### Version 2.2.1 
* Introduces automatic updates and also improves comments administration and fixes dropdown categories bug concerning unpublished items

### Version 2.3 
* Fixes the conflict between mootools/jquery which caused megamenu or/and google map not to work when certain templates are used.

### Version 2.4.0 
* Just a numbering convension to 3 level versioning

### Version 2.5.0 
Brings complete support for mobile versions (Android, iOS, REST services) of Improve-My-City, by third parties.

Mobile json wrapping is supported by Live+Gov European Research Programme

- Encrypt / Decrypt functionality
- Timestamp on DB changes
- Introduces geo-boundaries queries
 
Also v2.5.0 brings:
- New settings for displaying relative dates or plain dates with custom date format
- New settings to enable/disable json support for the new mobile-oriented controller
- Secret key on settings to be used for password encryption over http (It is highly recommended to use HTTPS)
- Remove login link on add new issue and add new comment
- Mega-menu css enhancements
- Fix administration approval issues (you can set on settings if you want to publish directly new issues or the administrator must first approve)
 