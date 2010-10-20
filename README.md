# trends #

Try out trends at <http://wg.vinayraikar.com/apps/trends/>


## Setup ##

* Replace USER-AGENT in index.php with your email or twitter id (so that @twitterapi can identify your app. And as they've said, the request limits would be higher for requests with useragents)
* Two json files, `trends.json` and `trends-processed.json` will be created in the same directory as index.php. Make sure the files are writable.
