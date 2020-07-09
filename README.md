# ActiveCollab to Jira® Migrator

*THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.*

## Important notes
If you're happy with this project, please help to improve it by solving issues,
writing pull-requests and other improvements.
Always keep in mind, what you get for free here and give back.

We do NOT suggest anyone to move away from the wonderful [ActiveCollab](https://activecollab.com/) which we loved for nealy a decade
and still love. Anyway structures changed for us and ActiveCollab wasn't the right tool for us anymore.
But it might be different for you and [ActiveCollab](https://activecollab.com/) is still a very very good choice & wonderful software with a wonderful team.
THANK YOU VERY MUCH @ActiveCollab Team for all your great work!

If you need to move from ActiveCollab to Jira®, this tool may help you to do that and keep most of your assets.

## Dependencies?
- PHP >=7.3
- Composer
- Jira® Server >= 8.x (tested with 8.10.0)
- ActiveCollab >= 5. (tested with ActiveCollab 6.2.135)

## What?
ActiveCollabToJiraMigrator provides an export tool with UI, based on PHP and the [ActiveCollab Feather SDK](https://github.com/activecollab/activecollab-feather-sdk) using the [ActiveCollab SDK](https://developers.activecollab.com/api-documentation/) to create imports for Jira® based on the (JSON import functionality)[https://confluence.atlassian.com/adminjiraserver089/importing-data-from-json-1005346888.html].

### Exported / importable entities:
Most of these are only partially supported, because the systems structure is different:
- User => Users
- Project => Project
- Task => Task
- Subtask => Sub-task
- Comment => Comment
- Task attachment => Task attachmant
- Tracked time => Worklog

#### Not supported for example:
- Companies
- Task Lists / Milestones (migrated as label)
- Comment files / attachments
- Reactions
- Notes
- Discussions
- Receipts
- ...

## How?
0. Read this project documentation and code to understand what it does and how
   it works.
1. Copy to your webserver and set /app as DocumentRoot for your VirtualHost
   like `thisactivecollabtojiramigrator.example.com` (Example)
2. Run `composer install` on the base directory via bash.
3. Copy `config/EXAMPLE.config.php` to `config/config.php`
4. Set all values in `config/config.php`, especially the _secret_
   *Keep your secret safe and secure!*
5. Open https://thisactivecollabtojiramigrator.example.com/index.php?secret=_secret_
   (Your secret from config.php) in your browser. You should see a form.
6. Enter your ActiveCollab Admin Credentials, set your offsets and limits (to allow splitting the export)
7. Export and check the results carefully

## Tips & Tricks
- Enable debug mode in config.php to see details and check for errors

## Important notes & limitations:
!! Due to limitations in Jira Import some ActiveCollab entities are not migrated at all. See below. !!
If you wish to add these functionalities, you may help to develop Export/JiraRestExporter which uses the Jira® API instead of the import.
Anyway some structural differences will never be 1:1 migratable like expenses, project expenses, non-task time records and others.
Here you'll need other workarounds or JiraApps which allow API-based creation.

!! Futhermore there are properties / settings which are not supported by Jira® Import which may also lead to unwanted access elevation. See below !!

## !! Important !!
1. The Jira® importer doesn't provide a sandbox environment. Data may be inconsistent if import fails.
So **TAKE BACKUPS** to be able to restore the clean state before the import!
2. Jira® has an issue with "External issue ID", which occurs if you split your import, see https://jira.atlassian.com/browse/JRASERVER-64477 - so you should try the workaround mentioned in this comment: https://jira.atlassian.com/browse/JRASERVER-64477?focusedCommentId=2274882&page=com.atlassian.jira.plugin.system.issuetabpanels:comment-tabpanel#comment-2274882 by Joe Harmon:
"There is another workaround that I found.  The external issue ID is created per project that you import.  The first time that I import it, I changed it to a global fields for any project.  After that, after I import it doesn't duplicate the field anymore." and set the custom field global after the first import.
Due to this issue it might also be a good idea to create only one large import file.

### No processing implemented for:
- Project expenses
- Project time records
- Project task lists
- Project notes
- Project files
- Task expenses
- Comment attachments
These entities are not migrated at all.

Furthermore the following properties / settings are not supported:
- Project roles
- Attachments hidden from client

# References:

## ActiveCollab API and SDK:
- https://developers.activecollab.com/api-documentation/
- https://github.com/activecollab/activecollab-feather-sdk

## Jira JSON Import:
- https://confluence.atlassian.com/adminjiraserver089/importing-data-from-json-1005346888.html

## Additional resources
- The Jira JSON import documentation doesn't seem to list all available fields. So if you need further fields, you should follow this documentation (https://confluence.atlassian.com/jirakb/how-to-enable-json-export-in-jira-server-723158427.html) to
enable JSON export in tasks and export some tasks to see the exported format, which seems to match the import format.

# Copyrights / Trademarks
All product names, logos, and brands are property of their respective owners.
All company, product and service names used are for identification purposes only.
Use of these names, logos, and brands does not imply endorsement.
- [ActiveCollab](https://activecollab.com/)
- [Atlassian®, Jira® and Confluence® are registered trademarks of Atlassian](https://www.atlassian.com/legal/trademark)
