RSSFit - Extendable XML news feed generator
Presented by Brandycoke Productions <http://www.brandycoke.com/>
Copyright (c) 2004-2006 NS Tai (aka tuff)

Information
----------------------
Current Version: 1.21
RSSFit is a module for XOOPS generates RSS 2.0 validated XML feed. Web masters can decide what to be displayed in the XML output by activating installed plug-ins.
Key features:
 - Plug-in system with several modules prepared
 - Plug-in example for module developers
 - RSS 2.0 validated Smarty template
 - Multi-byte languages compatible

System Requirements
----------------------
RSSFit 1.21: XOOPS 2.0.14
RSSFit 1.2: XOOPS 2.0.12-13.2 ; XOOPS Cube 2.0.12/13a
RSSFit 1.5 (Suspended): XOOPS 2.2.3a
PHP 4.3.x or later with (optional) mbstring extension installed for UTF-8 encoding conversion

Using RSSFit
----------------------
To install, go install it just like installing any module of XOOPS. You know you are smart enough to make it right.
For more information of installing a module of XOOPS, please refer to:
http://www.xoops.org/modules/smartfaq/faq.php?faqid=90

For detailed documentation please the RSSFit home page:
http://www.brandycoke.com/products/rssfit/

Who to Contact
----------------------
If you have any questions, comments or bug reports for this module, please register and post your message at:
http://www.brandycoke.com/home/modules/newbb/

Donations welcome
----------------------
If you think this module is useful, please consider making a donation to help us continue our work. You can support our future development by pointing your browser to the following hyperlink (all in one line):
https://www.paypal.com/xclick/business=donations%40brandycoke.com&item_name=Donation+for+Brandycoke+Freewares&item_number=rssfit&no_note=1&tax=0&currency_code=USD

Services
----------------------
If you are interested in custom solutions (exclusive modules/theme development, etc) please contact us at services@brandycoke.com.

Credits
----------------------
Programmed by NS Tai <tuff@brandycoke.com>
German language files translated by DocuAnt <http://www.DocuAnts.com/> 
Bulgarian language files translated by Stefan Ilivanov <http://www.xoopsbg.org/> 
French language files translated by Machenzy

Legal
----------------------
This program is released under the terms of the GNU General Public License as published by the Free Software Foundation.
You may not change or alter any portion of this comment or credits of supporting developers from this source code or any supporting source code which is considered copyrighted (c) material of the original comment or credit authors.
This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details. You should have received a copy of the GNU General Public License along with Liaise; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA

Version History
----------------------
Jul 25, 2006: Version 1.21
- Fixed compatibility problem with XOOPS 2.0.14
- Modified WF-Downloads plugin for padcasting (requires WF-Downloads version 3.1)
----------------------
Dec 23, 2005: Version 1.2 / 1.5
- (Version 1.5) Fixed incompatibility with XOOPS 2.2.3a
- New editable feed information: copyright
- PHP-debug messages are now completely hidden when prefernece "MIME type of RSS output" is set as "XML", even php-debug is turned on under system admin
- UTF-8 encoding conversion now requires PHP mbstring extension (Reference: http://www.php.net/manual/en/ref.mbstring.php)
- Updated plugins
- Plugins can now serve their own custom tags. (i.e. enclosure)
- Sticky text will now be hidden if either its title or content field is empty
- Contents of item-level's "description" elements are now sectioned using "CDATA" (Reference: http://blogs.law.harvard.edu/tech/encodingDescriptions)
- Fixed "call by reference" errors appear on PHP 4.4 / 5.1 
- German, Bulgarian, French language pack added (Thanks DocuAnt, Stefan Ilivanov, Machenzy)
----------------------
Mar 23, 2005: Version 1.1
Individual sub-feeds based on activated plug-ins
Editable channel elements
Sticky text displays as the very first item of a feed
New and updated plug-ins
Various bug fixes
Development sponsored by Stefanos Karagos <http://www.karagos.com/> and IIS-Resources <http://www.iis-resources.com/>

Notes for upgrading from version 1.0x: There is no upgrade patch since over 90 percent of the files are modified. You have to replace the entire directory contents with the new version. Don't forget to update the module after uploading new files. If you have the rss feed smarty template customized you should take a look at the file templates/rssfit_rss.html for corresponding changes.
----------------------
Dec 26, 2004: Version 1.03
Fixed incompatibility with PHP 5
Fixed rss template not validated by rss-validators
Fixed a typo in the mylinks plug-in
Dutch language pack added.
Italian language pack added.
Portuguese language pack added.
----------------------
Jun 6, 2004: Version 1.02
French language pack added.
----------------------
MAY 9, 2004: Version 1.01
Spanish language pack added.
----------------------
MAY 5, 2004: Version 1.0
First public release.
