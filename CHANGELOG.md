<a name="1.45.0"></a>
# [1.45.0](https://github.com/wherebyus/platformservice/compare/1.44.0...1.45.0) (2021-02-18)


### Bug Fixes

* 🐛 add accent color back ([3bb00fb](https://github.com/wherebyus/platformservice/commit/3bb00fb))
* 🐛 Adds a string replacer for an mj-image following h3 ([3306c65](https://github.com/wherebyus/platformservice/commit/3306c65))
* 🐛 Adds a string replacer for an mj-image with a closing p ([0570913](https://github.com/wherebyus/platformservice/commit/0570913))
* 🐛 Adds a string replacer for h2s followed by images ([187180a](https://github.com/wherebyus/platformservice/commit/187180a))
* 🐛 Adds fallback fonts to the MJML template ([4457ec7](https://github.com/wherebyus/platformservice/commit/4457ec7))
* 🐛 One last try ([22a215d](https://github.com/wherebyus/platformservice/commit/22a215d))
* 🐛 Reverts to using spacers for section separators ([dc365a0](https://github.com/wherebyus/platformservice/commit/dc365a0))
* 🐛 Rewraps the segment sections in an mj-spacer ([3b42155](https://github.com/wherebyus/platformservice/commit/3b42155))
* 🐛 Spark of inspiration: mj-raw ([4e743bd](https://github.com/wherebyus/platformservice/commit/4e743bd))
* 🐛 Typo ([14794d1](https://github.com/wherebyus/platformservice/commit/14794d1))
* remove depricated authorizeActionFromPassportStamp from PromotionMessageController ([11a9d0a](https://github.com/wherebyus/platformservice/commit/11a9d0a))
* use UserService::checkWhetherUserCanPerformAction for checking permissions ([ceb228a](https://github.com/wherebyus/platformservice/commit/ceb228a))


### Features

* 🎸 Moves the segment section tag into an mj class ([e6af548](https://github.com/wherebyus/platformservice/commit/e6af548))
* 🎸 When letters publish they now use the agnostic job ([0655ec4](https://github.com/wherebyus/platformservice/commit/0655ec4))


### BREAKING CHANGES

* 🧨 Channels need to have their defaults set or this shan't work

<a name="1.44.0"></a>
# [1.44.0](https://github.com/wherebyus/platformservice/compare/1.43.1...1.44.0) (2021-02-17)


### Bug Fixes

* [#375](https://github.com/wherebyus/platformservice/issues/375) pull request feedback ([61dc026](https://github.com/wherebyus/platformservice/commit/61dc026))
* 🐛 add email template ([#388](https://github.com/wherebyus/platformservice/issues/388)) ([fc0010c](https://github.com/wherebyus/platformservice/commit/fc0010c))
* 🐛 Adds defaults to constant contact properties ([1f85e1c](https://github.com/wherebyus/platformservice/commit/1f85e1c))
* 🐛 The Email template only updates programmaticaly ([a25ba9d](https://github.com/wherebyus/platformservice/commit/a25ba9d))


### Features

* 🎸 Adds a new job to send an email through an agnost esp ([e47c625](https://github.com/wherebyus/platformservice/commit/e47c625))
* 🎸 Adds a new way for MailChimpRepository to send a campgn ([165bdb2](https://github.com/wherebyus/platformservice/commit/165bdb2))
* 🎸 Adds ESP choice information to the Letter itself ([ebe5519](https://github.com/wherebyus/platformservice/commit/ebe5519))
* 🎸 Adds the status query parameter to promotions ([e05e108](https://github.com/wherebyus/platformservice/commit/e05e108))
* 🎸 Provides a simple private feed for accessing promotions ([549c01c](https://github.com/wherebyus/platformservice/commit/549c01c))
* adds emailServiceProvider and listId to the Letter ([9966692](https://github.com/wherebyus/platformservice/commit/9966692))

<a name="1.43.1"></a>
## [1.43.1](https://github.com/wherebyus/platformservice/compare/1.43.0...1.43.1) (2021-02-11)

<a name="1.43.0"></a>
# [1.43.0](https://github.com/wherebyus/platformservice/compare/1.42.0...1.43.0) (2021-02-10)


### Bug Fixes

* 🐛 Json encodes the daysPublished data when creating a type ([e0b2aaf](https://github.com/wherebyus/platformservice/commit/e0b2aaf))


### Features

* 🎸 Adds base styles to the MJML template ([5400caa](https://github.com/wherebyus/platformservice/commit/5400caa))

<a name="1.42.0"></a>
# [1.42.0](https://github.com/wherebyus/platformservice/compare/1.41.0...1.42.0) (2021-02-08)


### Bug Fixes

* 🐛 Adds improved error responses from MailChimp ([0a3a571](https://github.com/wherebyus/platformservice/commit/0a3a571))
* 🐛 MJMLFormatter constructs more complex mjml ([4296b07](https://github.com/wherebyus/platformservice/commit/4296b07))


### Features

* 🎸 Adds an APPROVED FOR PUBLICATION Status ([885a1cb](https://github.com/wherebyus/platformservice/commit/885a1cb))
* 🎸 Adds new API to support Promos with differing statuses ([f7a6d18](https://github.com/wherebyus/platformservice/commit/f7a6d18))
* 🎸 API users can now support AdType custom schedules ([8cc6ade](https://github.com/wherebyus/platformservice/commit/8cc6ade))
* 🎸 PlatformService can now leave messages about services ([ab1b395](https://github.com/wherebyus/platformservice/commit/ab1b395))
* 🎸 The new Letter test method is now ESP agnostic ([764c3a9](https://github.com/wherebyus/platformservice/commit/764c3a9))

<a name="1.41.0"></a>
# [1.41.0](https://github.com/wherebyus/platformservice/compare/1.38.0...1.41.0) (2021-02-02)


### Features

* 🎸 new endpoint to get available dates by ad types ([#357](https://github.com/wherebyus/platformservice/issues/357)) ([d3eee13](https://github.com/wherebyus/platformservice/commit/d3eee13))

<a name="1.40.0"></a>
# [1.40.0](https://github.com/wherebyus/platformservice/compare/1.38.0...1.40.0) (2021-02-02)


### Features

* 🎸 new endpoint to get available dates by ad types ([#357](https://github.com/wherebyus/platformservice/issues/357)) ([d3eee13](https://github.com/wherebyus/platformservice/commit/d3eee13))

<a name="1.39.0"></a>
# [1.39.0](https://github.com/wherebyus/platformservice/compare/1.38.0...1.39.0) (2021-01-28)


### Features

* 🎸 new endpoint to get available dates by ad types ([#357](https://github.com/wherebyus/platformservice/issues/357)) ([d3eee13](https://github.com/wherebyus/platformservice/commit/d3eee13))

<a name="1.38.0"></a>
# [1.38.0](https://github.com/wherebyus/platformservice/compare/1.37.2...1.38.0) (2021-01-28)


### Features

* 🎸 new endpoint to retrieve promo card data ([#356](https://github.com/wherebyus/platformservice/issues/356)) ([9fbbd94](https://github.com/wherebyus/platformservice/commit/9fbbd94))

<a name="1.37.2"></a>
## [1.37.2](https://github.com/wherebyus/platformservice/compare/1.37.1...1.37.2) (2021-01-27)


### Bug Fixes

* 🐛 The dom is now part of the letter template response ([140245c](https://github.com/wherebyus/platformservice/commit/140245c))
* passes the DOM string to the test email ([c75e6ba](https://github.com/wherebyus/platformservice/commit/c75e6ba))


### Features

* 🎸 1.37.1 ([5f11662](https://github.com/wherebyus/platformservice/commit/5f11662))

<a name="1.37.0"></a>
# [1.37.0](https://github.com/wherebyus/platformservice/compare/1.35.1...1.37.0) (2021-01-26)


### Bug Fixes

* 🐛 Corrects typo in test function argument ([eaccd49](https://github.com/wherebyus/platformservice/commit/eaccd49))
* 🐛 Improves response rollbar error ([002cb5c](https://github.com/wherebyus/platformservice/commit/002cb5c))
* 🐛 Re-adds channel link accent color ([2035ed9](https://github.com/wherebyus/platformservice/commit/2035ed9))
* 🐛 The MJMLTemplateRepository now sends meaningful response ([20473ca](https://github.com/wherebyus/platformservice/commit/20473ca))


### Features

* 🎸 Improves usefulness of template generating email msg ([e832c8d](https://github.com/wherebyus/platformservice/commit/e832c8d))
* 🎸 The Response class will now log its Rollbar error ([f4af2f4](https://github.com/wherebyus/platformservice/commit/f4af2f4))



<a name="1.35.0"></a>
# [1.35.0](https://github.com/wherebyus/platformservice/compare/1.34.4...1.35.0) (2021-01-19)

<a name="1.35.0"></a>
# [1.35.0](https://github.com/wherebyus/platformservice/compare/1.34.4...1.35.0) (2021-01-19)


### Features

* 🎸 Adds mjml support to the get promotions  controller ([5b03049](https://github.com/wherebyus/platformservice/commit/5b03049))
* 🎸 Adds support for retrieving a promotion's mjml ([34d91d9](https://github.com/wherebyus/platformservice/commit/34d91d9))
* 🎸 ESP basic structure ([#336](https://github.com/wherebyus/platformservice/issues/336)) ([8bdc772](https://github.com/wherebyus/platformservice/commit/8bdc772))


<a name="1.34.0"></a>
# [1.34.0](https://github.com/wherebyus/platformservice/compare/1.33.1...1.34.0) (2020-12-17)


### Bug Fixes

* 🐛 catch null orderIds ([0eceba6](https://github.com/wherebyus/platformservice/commit/0eceba6))
* 🐛 change default to true ([#305](https://github.com/wherebyus/platformservice/issues/305)) ([148a485](https://github.com/wherebyus/platformservice/commit/148a485))
* 🐛 clean up orderId nullcheck ([1042441](https://github.com/wherebyus/platformservice/commit/1042441))
* 🐛 improve test, remove cruft, simplify endpoints ([ef8da46](https://github.com/wherebyus/platformservice/commit/ef8da46))
* 🐛 improve usability and return only necessary info ([7bda2e8](https://github.com/wherebyus/platformservice/commit/7bda2e8))
* 🐛 remove commented code ([4c71559](https://github.com/wherebyus/platformservice/commit/4c71559))


### Features

* 🎸 new method to call for ad credit by promo id ([4f8215d](https://github.com/wherebyus/platformservice/commit/4f8215d))



<a name="1.32.0"></a>
# [1.32.0](https://github.com/wherebyus/platformservice/compare/1.31.1...1.32.0) (2020-12-14)


### Bug Fixes

* 🐛 Updates the default advertisingRevenueShare to 5% ([1c35969](https://github.com/wherebyus/platformservice/commit/1c35969))

<a name="1.35.0"></a>
# [1.35.0](https://github.com/wherebyus/platformservice/compare/1.33.1...1.35.0) (2020-12-17)


### Bug Fixes

* 🐛 catch null orderIds ([0eceba6](https://github.com/wherebyus/platformservice/commit/0eceba6))
* 🐛 change default to true ([#305](https://github.com/wherebyus/platformservice/issues/305)) ([148a485](https://github.com/wherebyus/platformservice/commit/148a485))
* 🐛 clean up orderId nullcheck ([1042441](https://github.com/wherebyus/platformservice/commit/1042441))
* 🐛 improve test, remove cruft, simplify endpoints ([ef8da46](https://github.com/wherebyus/platformservice/commit/ef8da46))
* 🐛 improve usability and return only necessary info ([7bda2e8](https://github.com/wherebyus/platformservice/commit/7bda2e8))
* 🐛 remove commented code ([4c71559](https://github.com/wherebyus/platformservice/commit/4c71559))


### Features

* 🎸 new method to call for ad credit by promo id ([4f8215d](https://github.com/wherebyus/platformservice/commit/4f8215d))



<a name="1.32.0"></a>
# [1.32.0](https://github.com/wherebyus/platformservice/compare/1.31.1...1.32.0) (2020-12-14)


### Bug Fixes

* 🐛 Updates the default advertisingRevenueShare to 5% ([1c35969](https://github.com/wherebyus/platformservice/commit/1c35969))

<a name="1.34.0"></a>
# [1.34.0](https://github.com/wherebyus/platformservice/compare/1.33.1...1.34.0) (2020-12-17)


### Bug Fixes

* 🐛 catch null orderIds ([0eceba6](https://github.com/wherebyus/platformservice/commit/0eceba6))
* 🐛 change default to true ([#305](https://github.com/wherebyus/platformservice/issues/305)) ([148a485](https://github.com/wherebyus/platformservice/commit/148a485))
* 🐛 clean up orderId nullcheck ([1042441](https://github.com/wherebyus/platformservice/commit/1042441))
* 🐛 improve test, remove cruft, simplify endpoints ([ef8da46](https://github.com/wherebyus/platformservice/commit/ef8da46))
* 🐛 improve usability and return only necessary info ([7bda2e8](https://github.com/wherebyus/platformservice/commit/7bda2e8))
* 🐛 remove commented code ([4c71559](https://github.com/wherebyus/platformservice/commit/4c71559))


### Features

* 🎸 new method to call for ad credit by promo id ([4f8215d](https://github.com/wherebyus/platformservice/commit/4f8215d))



<a name="1.32.0"></a>
# [1.32.0](https://github.com/wherebyus/platformservice/compare/1.31.1...1.32.0) (2020-12-14)


### Bug Fixes

* 🐛 Updates the default advertisingRevenueShare to 5% ([1c35969](https://github.com/wherebyus/platformservice/commit/1c35969))

<a name="1.32.0"></a>
# [1.32.0](https://github.com/wherebyus/platformservice/compare/1.31.1...1.32.0) (2020-12-14)


### Bug Fixes

* 🐛 Adds a max-width style to the img tag on the template ([ef8bf0a](https://github.com/wherebyus/platformservice/commit/ef8bf0a))
* 🐛 Templates are no longer directly update-able ([f47fa4b](https://github.com/wherebyus/platformservice/commit/f47fa4b))
* 🐛 Updates the default advertisingRevenueShare to 5% ([1c35969](https://github.com/wherebyus/platformservice/commit/1c35969))


### Features

* 🎸 Adds a throughpoint to update the promotion type temp ([3595fe1](https://github.com/wherebyus/platformservice/commit/3595fe1))

<a name="1.31.0"></a>
# [1.31.0](https://github.com/wherebyus/platformservice/compare/1.30.0...1.31.0) (2020-12-07)


### Bug Fixes

* 🐛 add api key check to middleware ([#282](https://github.com/wherebyus/platformservice/issues/282)) ([c3408ae](https://github.com/wherebyus/platformservice/commit/c3408ae))

<a name="1.30.1"></a>
## [1.30.1](https://github.com/wherebyus/platformservice/compare/1.30.0...1.30.1) (2020-12-07)


### Bug Fixes

* 🐛 add api key check to middleware ([#282](https://github.com/wherebyus/platformservice/issues/282)) ([c3408ae](https://github.com/wherebyus/platformservice/commit/c3408ae))

<a name="1.30.0"></a>
# [1.30.0](https://github.com/wherebyus/platformservice/compare/1.28.1...1.30.0) (2020-12-04)

### Features

* 🎸 add autoUpdateChannelStatsFromMailchimp ([#279](https://github.com/wherebyus/platformservice/issues/279)) ([02daa32](https://github.com/wherebyus/platformservice/commit/02daa32))
* 🎸 Adds a new v2 promotion api that takes a key only ([#283](https://github.com/wherebyus/platformservice/issues/283)) ([11ae915](https://github.com/wherebyus/platformservice/commit/11ae915))
* 🎸 Adds special promotion styles to the template ([12d0302](https://github.com/wherebyus/platformservice/commit/12d0302))

<a name="1.29.0"></a>
# [1.29.0](https://github.com/wherebyus/platformservice/compare/1.28.1...1.29.0) (2020-12-01)

### Features

* 🎸 add autoUpdateChannelStatsFromMailchimp ([#279](https://github.com/wherebyus/platformservice/issues/279)) ([02daa32](https://github.com/wherebyus/platformservice/commit/02daa32))
* 🎸 Adds special promotion styles to the template ([12d0302](https://github.com/wherebyus/platformservice/commit/12d0302))

<a name="1.28.6"></a>
## [1.28.6](https://github.com/wherebyus/platformservice/compare/1.28.1...1.28.6) (2020-11-24)


### Bug Fixes

### Features

* 🎸 Adds special promotion styles to the template ([12d0302](https://github.com/wherebyus/platformservice/commit/12d0302))

<a name="1.28.5"></a>
## [1.28.5](https://github.com/wherebyus/platformservice/compare/1.28.1...1.28.5) (2020-11-24)


### Bug Fixes

* 🐛 Adds spacing between the footer and end of content ([be68bbc](https://github.com/wherebyus/platformservice/commit/be68bbc))
* 🐛 Improves the font sizing in our authoring newsletter ([4b5177f](https://github.com/wherebyus/platformservice/commit/4b5177f))
* 🐛 Improves the font sizing in our authoring newsletter ([#267](https://github.com/wherebyus/platformservice/issues/267)) ([9aac503](https://github.com/wherebyus/platformservice/commit/9aac503))
* 🐛 MailChimp lists greater than 10 will now work ([8c6518b](https://github.com/wherebyus/platformservice/commit/8c6518b))
* 🐛 Removes the Title from the AdService post ([82e5b99](https://github.com/wherebyus/platformservice/commit/82e5b99))
* 🐛 set publicatioStatus of new letter to fake date ([d4c9395](https://github.com/wherebyus/platformservice/commit/d4c9395))
* 🐛 The title, subtitle, and authors are now all assumed to ([36eabfd](https://github.com/wherebyus/platformservice/commit/36eabfd))
* 🐛 Updates list styles ([9be1e11](https://github.com/wherebyus/platformservice/commit/9be1e11))

<a name="1.28.4"></a>
## [1.28.4](https://github.com/wherebyus/platformservice/compare/1.28.0...1.28.4) (2020-11-23)


### Bug Fixes

* 🐛 adds encoding ([2c16228](https://github.com/wherebyus/platformservice/commit/2c16228))
* 🐛 Doesn't pass an empty copy rendered ([bb42c59](https://github.com/wherebyus/platformservice/commit/bb42c59))
* 🐛 MailChimp lists greater than 10 will now work ([8c6518b](https://github.com/wherebyus/platformservice/commit/8c6518b))
* 🐛 Moves the render stripper into the controller ([b235828](https://github.com/wherebyus/platformservice/commit/b235828))


### Features

* 🎸 Adds copy rendered to the letter array ([c67c9c7](https://github.com/wherebyus/platformservice/commit/c67c9c7))
* 🎸 moves the preview to the top of the body ([e7eed72](https://github.com/wherebyus/platformservice/commit/e7eed72))



<a name="1.27.1"></a>
## [1.27.1](https://github.com/wherebyus/platformservice/compare/1.22.2...1.27.1) (2020-11-18)

<a name="1.28.3"></a>
## [1.28.3](https://github.com/wherebyus/platformservice/compare/1.28.1...1.28.3) (2020-11-19)


### Bug Fixes

* 🐛 Adds spacing between the footer and end of content ([be68bbc](https://github.com/wherebyus/platformservice/commit/be68bbc))
* 🐛 Improves the font sizing in our authoring newsletter ([4b5177f](https://github.com/wherebyus/platformservice/commit/4b5177f))
* 🐛 Improves the font sizing in our authoring newsletter ([#267](https://github.com/wherebyus/platformservice/issues/267)) ([9aac503](https://github.com/wherebyus/platformservice/commit/9aac503))
* 🐛 Updates list styles ([9be1e11](https://github.com/wherebyus/platformservice/commit/9be1e11))

<a name="1.28.2"></a>
## [1.28.2](https://github.com/wherebyus/platformservice/compare/1.28.1...1.28.2) (2020-11-19)


### Bug Fixes

* 🐛 Adds spacing between the footer and end of content ([be68bbc](https://github.com/wherebyus/platformservice/commit/be68bbc))
* 🐛 Improves the font sizing in our authoring newsletter ([4b5177f](https://github.com/wherebyus/platformservice/commit/4b5177f))
* 🐛 Improves the font sizing in our authoring newsletter ([#267](https://github.com/wherebyus/platformservice/issues/267)) ([9aac503](https://github.com/wherebyus/platformservice/commit/9aac503))

>>>>>>> master
<a name="1.28.0"></a>
# [1.28.0](https://github.com/wherebyus/platformservice/compare/1.22.2...1.28.0) (2020-11-18)


<a name="1.27.1"></a>
## [1.27.1](https://github.com/wherebyus/platformservice/compare/1.22.2...1.27.1) (2020-11-18)



### Bug Fixes

* 🐛 Adds a copy rendered lookup to the newsletter blade ([c0810f9](https://github.com/wherebyus/platformservice/commit/c0810f9))
* 🐛 Adds a resolveContent parameter to the public api ([4af666c](https://github.com/wherebyus/platformservice/commit/4af666c))
* 🐛 Adds an isset check to the unique id ([ceffcd6](https://github.com/wherebyus/platformservice/commit/ceffcd6))
* 🐛 Cache bust ([dc91677](https://github.com/wherebyus/platformservice/commit/dc91677))
* 🐛 change reschedule email heading ([#259](https://github.com/wherebyus/platformservice/issues/259)) ([e7f1e83](https://github.com/wherebyus/platformservice/commit/e7f1e83))
* 🐛 Copy rendered will now return string if not set ([400daf8](https://github.com/wherebyus/platformservice/commit/400daf8))
* 🐛 correct private properties ([6de5848](https://github.com/wherebyus/platformservice/commit/6de5848))
* 🐛 correct sales email address ([3047198](https://github.com/wherebyus/platformservice/commit/3047198))
* 🐛 Corrects byline ([5fb1379](https://github.com/wherebyus/platformservice/commit/5fb1379))
* 🐛 Corrects some type errors ([f4e907d](https://github.com/wherebyus/platformservice/commit/f4e907d))
* 🐛 Corrects type error with uninstantiable MailChimp ([16fc336](https://github.com/wherebyus/platformservice/commit/16fc336))
* 🐛 Corrects typo in letter cache forgetter ([900cfdb](https://github.com/wherebyus/platformservice/commit/900cfdb))
* 🐛 create new job to send order notification to creators ([d49d30e](https://github.com/wherebyus/platformservice/commit/d49d30e))
* 🐛 Does some shoring up around send letter tests ([962dea0](https://github.com/wherebyus/platformservice/commit/962dea0))
* 🐛 fix (bool) string is always true ([#252](https://github.com/wherebyus/platformservice/issues/252)) ([d3b3edd](https://github.com/wherebyus/platformservice/commit/d3b3edd))
* 🐛 Fixes an error preventing a letter from updating ([ec19799](https://github.com/wherebyus/platformservice/commit/ec19799))
* 🐛 Fixes shameful typo in LetterController ([2a428c0](https://github.com/wherebyus/platformservice/commit/2a428c0))
* 🐛 Letter will now accept an empty publicationDate ([b7085f7](https://github.com/wherebyus/platformservice/commit/b7085f7))
* 🐛 Newsletters should now render html from the database ([931b8bf](https://github.com/wherebyus/platformservice/commit/931b8bf))
* 🐛 Passes markup as a string rather than a response obj ([876439e](https://github.com/wherebyus/platformservice/commit/876439e))
* 🐛 Removes a reference to the MailChimpFacadeInterface ([8924840](https://github.com/wherebyus/platformservice/commit/8924840))
* 🐛 Removes stray curly braces in the blade template ([b5ce6b2](https://github.com/wherebyus/platformservice/commit/b5ce6b2))
* 🐛 Removes the uniqueId from the letter update ([28de34a](https://github.com/wherebyus/platformservice/commit/28de34a))
* 🐛 Removes Y-m-d validation rules for publication date ([de99616](https://github.com/wherebyus/platformservice/commit/de99616))
* 🐛 Slug is no longer required ([4311ed0](https://github.com/wherebyus/platformservice/commit/4311ed0))
* 🐛 the Letter heading is no longer required ([6fcaa2c](https://github.com/wherebyus/platformservice/commit/6fcaa2c))
* 🐛 The update letter method will now return a proper error ([7c3abd6](https://github.com/wherebyus/platformservice/commit/7c3abd6))
* 🐛 Updating a letter will now destroy its cache ([3826880](https://github.com/wherebyus/platformservice/commit/3826880))
* 🐛 We'll lookup the specific letter in the MailChimp job ([67cf692](https://github.com/wherebyus/platformservice/commit/67cf692))
* 🐛 When a new letter is created, we will reset the cache ([b14db80](https://github.com/wherebyus/platformservice/commit/b14db80))
* 🐛 When an email is sent, the letter wil increment to pub ([487fc71](https://github.com/wherebyus/platformservice/commit/487fc71))
* add default url ([14ec304](https://github.com/wherebyus/platformservice/commit/14ec304))
* adds test and sends emails; ([94d2333](https://github.com/wherebyus/platformservice/commit/94d2333))
* correct merge error ([2428be4](https://github.com/wherebyus/platformservice/commit/2428be4))
* correct test ([7414921](https://github.com/wherebyus/platformservice/commit/7414921))
* finish writing test ([9bc890b](https://github.com/wherebyus/platformservice/commit/9bc890b))
* npm install ([bf23b90](https://github.com/wherebyus/platformservice/commit/bf23b90))
* remove unnecessary code, fix langauge ([a3d4048](https://github.com/wherebyus/platformservice/commit/a3d4048))


### Features

* 🎸 1.22.2 ([d71076b](https://github.com/wherebyus/platformservice/commit/d71076b))
* 🎸 add a byline to email template ([#243](https://github.com/wherebyus/platformservice/issues/243)) ([fbbbedb](https://github.com/wherebyus/platformservice/commit/fbbbedb))

* 🎸 add copyright, mailing address...to footer ([#253](https://github.com/wherebyus/platformservice/issues/253)) ([12ad9ce](https://github.com/wherebyus/platformservice/commit/12ad9ce))
* 🎸 added newsletterUrl to channelconfiguration ([b4867f6](https://github.com/wherebyus/platformservice/commit/b4867f6))
* 🎸 Adds a copyRendered property to the letter ([0baccae](https://github.com/wherebyus/platformservice/commit/0baccae))
* 🎸 Adds a mailchimp sending job ([5df03fd](https://github.com/wherebyus/platformservice/commit/5df03fd))
* 🎸 Adds an API endpoint for getting a channel's authors ([#242](https://github.com/wherebyus/platformservice/issues/242)) ([517b33b](https://github.com/wherebyus/platformservice/commit/517b33b))
* 🎸 Adds positioning to AdTypeService ([28143de](https://github.com/wherebyus/platformservice/commit/28143de))
* 🎸 Adds scheduled and template statuses ([a2a9b6a](https://github.com/wherebyus/platformservice/commit/a2a9b6a))
* 🎸 Adds the date query parameter to the ads api ([7593ee5](https://github.com/wherebyus/platformservice/commit/7593ee5))
* 🎸 Enables the promotion type scaffolding endpoint ([#246](https://github.com/wherebyus/platformservice/issues/246)) ([51a7502](https://github.com/wherebyus/platformservice/commit/51a7502))


* 🎸 send notification when a promo is rescheduled ([#258](https://github.com/wherebyus/platformservice/issues/258)) ([25ff0e3](https://github.com/wherebyus/platformservice/commit/25ff0e3))
* 🎸 wordmark is the banner ([#254](https://github.com/wherebyus/platformservice/issues/254)) ([a0ccc3d](https://github.com/wherebyus/platformservice/commit/a0ccc3d))
* adds a channel scaffold api ([c58d6d5](https://github.com/wherebyus/platformservice/commit/c58d6d5))

<a name="1.27.0"></a>
# [1.27.0](https://github.com/wherebyus/platformservice/compare/1.22.2...1.27.0) (2020-11-17)


### Bug Fixes

* 🐛 Adds a copy rendered lookup to the newsletter blade ([c0810f9](https://github.com/wherebyus/platformservice/commit/c0810f9))
* 🐛 Adds a resolveContent parameter to the public api ([4af666c](https://github.com/wherebyus/platformservice/commit/4af666c))
* 🐛 Adds an isset check to the unique id ([ceffcd6](https://github.com/wherebyus/platformservice/commit/ceffcd6))
* 🐛 Cache bust ([dc91677](https://github.com/wherebyus/platformservice/commit/dc91677))
* 🐛 change reschedule email heading ([#259](https://github.com/wherebyus/platformservice/issues/259)) ([e7f1e83](https://github.com/wherebyus/platformservice/commit/e7f1e83))
* 🐛 Copy rendered will now return string if not set ([400daf8](https://github.com/wherebyus/platformservice/commit/400daf8))
* 🐛 correct private properties ([6de5848](https://github.com/wherebyus/platformservice/commit/6de5848))
* 🐛 correct sales email address ([3047198](https://github.com/wherebyus/platformservice/commit/3047198))
* 🐛 Corrects some type errors ([f4e907d](https://github.com/wherebyus/platformservice/commit/f4e907d))
* 🐛 Corrects type error with uninstantiable MailChimp ([16fc336](https://github.com/wherebyus/platformservice/commit/16fc336))
* 🐛 Corrects typo in letter cache forgetter ([900cfdb](https://github.com/wherebyus/platformservice/commit/900cfdb))
* 🐛 create new job to send order notification to creators ([d49d30e](https://github.com/wherebyus/platformservice/commit/d49d30e))
* 🐛 Does some shoring up around send letter tests ([962dea0](https://github.com/wherebyus/platformservice/commit/962dea0))
* 🐛 fix (bool) string is always true ([#252](https://github.com/wherebyus/platformservice/issues/252)) ([d3b3edd](https://github.com/wherebyus/platformservice/commit/d3b3edd))
* 🐛 Fixes an error preventing a letter from updating ([ec19799](https://github.com/wherebyus/platformservice/commit/ec19799))
* 🐛 Fixes shameful typo in LetterController ([2a428c0](https://github.com/wherebyus/platformservice/commit/2a428c0))
* 🐛 Letter will now accept an empty publicationDate ([b7085f7](https://github.com/wherebyus/platformservice/commit/b7085f7))
* 🐛 Newsletters should now render html from the database ([931b8bf](https://github.com/wherebyus/platformservice/commit/931b8bf))
* 🐛 Passes markup as a string rather than a response obj ([876439e](https://github.com/wherebyus/platformservice/commit/876439e))
* 🐛 Removes a reference to the MailChimpFacadeInterface ([8924840](https://github.com/wherebyus/platformservice/commit/8924840))
* 🐛 Removes stray curly braces in the blade template ([b5ce6b2](https://github.com/wherebyus/platformservice/commit/b5ce6b2))
* 🐛 Removes the uniqueId from the letter update ([28de34a](https://github.com/wherebyus/platformservice/commit/28de34a))
* 🐛 Removes Y-m-d validation rules for publication date ([de99616](https://github.com/wherebyus/platformservice/commit/de99616))
* 🐛 Slug is no longer required ([4311ed0](https://github.com/wherebyus/platformservice/commit/4311ed0))
* 🐛 the Letter heading is no longer required ([6fcaa2c](https://github.com/wherebyus/platformservice/commit/6fcaa2c))
* 🐛 The update letter method will now return a proper error ([7c3abd6](https://github.com/wherebyus/platformservice/commit/7c3abd6))
* 🐛 Updating a letter will now destroy its cache ([3826880](https://github.com/wherebyus/platformservice/commit/3826880))
* 🐛 When a new letter is created, we will reset the cache ([b14db80](https://github.com/wherebyus/platformservice/commit/b14db80))
* 🐛 When an email is sent, the letter wil increment to pub ([487fc71](https://github.com/wherebyus/platformservice/commit/487fc71))
* add default url ([14ec304](https://github.com/wherebyus/platformservice/commit/14ec304))
* adds test and sends emails; ([94d2333](https://github.com/wherebyus/platformservice/commit/94d2333))
* correct merge error ([2428be4](https://github.com/wherebyus/platformservice/commit/2428be4))
* correct test ([7414921](https://github.com/wherebyus/platformservice/commit/7414921))
* finish writing test ([9bc890b](https://github.com/wherebyus/platformservice/commit/9bc890b))
* npm install ([bf23b90](https://github.com/wherebyus/platformservice/commit/bf23b90))
* remove unnecessary code, fix langauge ([a3d4048](https://github.com/wherebyus/platformservice/commit/a3d4048))


### Features

* 🎸 1.22.2 ([d71076b](https://github.com/wherebyus/platformservice/commit/d71076b))
* 🎸 add a byline to email template ([#243](https://github.com/wherebyus/platformservice/issues/243)) ([fbbbedb](https://github.com/wherebyus/platformservice/commit/fbbbedb))
* 🎸 add copyright, mailing address...to footer ([#253](https://github.com/wherebyus/platformservice/issues/253)) ([12ad9ce](https://github.com/wherebyus/platformservice/commit/12ad9ce))
* 🎸 added newsletterUrl to channelconfiguration ([b4867f6](https://github.com/wherebyus/platformservice/commit/b4867f6))
* 🎸 Adds a copyRendered property to the letter ([0baccae](https://github.com/wherebyus/platformservice/commit/0baccae))
* 🎸 Adds a mailchimp sending job ([5df03fd](https://github.com/wherebyus/platformservice/commit/5df03fd))
* 🎸 Adds an API endpoint for getting a channel's authors ([#242](https://github.com/wherebyus/platformservice/issues/242)) ([517b33b](https://github.com/wherebyus/platformservice/commit/517b33b))
* 🎸 Adds positioning to AdTypeService ([28143de](https://github.com/wherebyus/platformservice/commit/28143de))
* 🎸 Adds scheduled and template statuses ([a2a9b6a](https://github.com/wherebyus/platformservice/commit/a2a9b6a))
* 🎸 Adds the date query parameter to the ads api ([7593ee5](https://github.com/wherebyus/platformservice/commit/7593ee5))
* 🎸 Enables the promotion type scaffolding endpoint ([#246](https://github.com/wherebyus/platformservice/issues/246)) ([51a7502](https://github.com/wherebyus/platformservice/commit/51a7502))
* 🎸 send notification when a promo is rescheduled ([#258](https://github.com/wherebyus/platformservice/issues/258)) ([25ff0e3](https://github.com/wherebyus/platformservice/commit/25ff0e3))
* 🎸 wordmark is the banner ([#254](https://github.com/wherebyus/platformservice/issues/254)) ([a0ccc3d](https://github.com/wherebyus/platformservice/commit/a0ccc3d))
* adds a channel scaffold api ([c58d6d5](https://github.com/wherebyus/platformservice/commit/c58d6d5))

<a name="1.25.0"></a>
# [1.25.0](https://github.com/wherebyus/platformservice/compare/1.22.2...1.25.0) (2020-11-13)


### Bug Fixes

* 🐛 Adds a copy rendered lookup to the newsletter blade ([c0810f9](https://github.com/wherebyus/platformservice/commit/c0810f9))
* 🐛 Adds a resolveContent parameter to the public api ([4af666c](https://github.com/wherebyus/platformservice/commit/4af666c))
* 🐛 Adds an isset check to the unique id ([ceffcd6](https://github.com/wherebyus/platformservice/commit/ceffcd6))
* 🐛 Cache bust ([dc91677](https://github.com/wherebyus/platformservice/commit/dc91677))
* 🐛 Copy rendered will now return string if not set ([400daf8](https://github.com/wherebyus/platformservice/commit/400daf8))
* 🐛 correct private properties ([6de5848](https://github.com/wherebyus/platformservice/commit/6de5848))
* 🐛 correct sales email address ([3047198](https://github.com/wherebyus/platformservice/commit/3047198))
* 🐛 Corrects some type errors ([f4e907d](https://github.com/wherebyus/platformservice/commit/f4e907d))
* 🐛 Corrects typo in letter cache forgetter ([900cfdb](https://github.com/wherebyus/platformservice/commit/900cfdb))
* 🐛 create new job to send order notification to creators ([d49d30e](https://github.com/wherebyus/platformservice/commit/d49d30e))
* 🐛 Does some shoring up around send letter tests ([962dea0](https://github.com/wherebyus/platformservice/commit/962dea0))
* 🐛 fix (bool) string is always true ([#252](https://github.com/wherebyus/platformservice/issues/252)) ([d3b3edd](https://github.com/wherebyus/platformservice/commit/d3b3edd))
* 🐛 Fixes an error preventing a letter from updating ([ec19799](https://github.com/wherebyus/platformservice/commit/ec19799))
* 🐛 Fixes shameful typo in LetterController ([2a428c0](https://github.com/wherebyus/platformservice/commit/2a428c0))
* 🐛 Letter will now accept an empty publicationDate ([b7085f7](https://github.com/wherebyus/platformservice/commit/b7085f7))
* 🐛 Newsletters should now render html from the database ([931b8bf](https://github.com/wherebyus/platformservice/commit/931b8bf))
* 🐛 Removes stray curly braces in the blade template ([b5ce6b2](https://github.com/wherebyus/platformservice/commit/b5ce6b2))
* 🐛 Removes the uniqueId from the letter update ([28de34a](https://github.com/wherebyus/platformservice/commit/28de34a))
* 🐛 Removes Y-m-d validation rules for publication date ([de99616](https://github.com/wherebyus/platformservice/commit/de99616))
* 🐛 Slug is no longer required ([4311ed0](https://github.com/wherebyus/platformservice/commit/4311ed0))
* 🐛 the Letter heading is no longer required ([6fcaa2c](https://github.com/wherebyus/platformservice/commit/6fcaa2c))
* 🐛 The update letter method will now return a proper error ([7c3abd6](https://github.com/wherebyus/platformservice/commit/7c3abd6))
* 🐛 Updating a letter will now destroy its cache ([3826880](https://github.com/wherebyus/platformservice/commit/3826880))
* 🐛 When a new letter is created, we will reset the cache ([b14db80](https://github.com/wherebyus/platformservice/commit/b14db80))
* add default url ([14ec304](https://github.com/wherebyus/platformservice/commit/14ec304))
* adds test and sends emails; ([94d2333](https://github.com/wherebyus/platformservice/commit/94d2333))
* correct merge error ([2428be4](https://github.com/wherebyus/platformservice/commit/2428be4))
* correct test ([7414921](https://github.com/wherebyus/platformservice/commit/7414921))
* finish writing test ([9bc890b](https://github.com/wherebyus/platformservice/commit/9bc890b))
* npm install ([bf23b90](https://github.com/wherebyus/platformservice/commit/bf23b90))
* remove unnecessary code, fix langauge ([a3d4048](https://github.com/wherebyus/platformservice/commit/a3d4048))


### Features

* 🎸 1.22.2 ([d71076b](https://github.com/wherebyus/platformservice/commit/d71076b))
* 🎸 add a byline to email template ([#243](https://github.com/wherebyus/platformservice/issues/243)) ([fbbbedb](https://github.com/wherebyus/platformservice/commit/fbbbedb))
* 🎸 add copyright, mailing address...to footer ([#253](https://github.com/wherebyus/platformservice/issues/253)) ([12ad9ce](https://github.com/wherebyus/platformservice/commit/12ad9ce))
* 🎸 Adds a copyRendered property to the letter ([0baccae](https://github.com/wherebyus/platformservice/commit/0baccae))
* 🎸 Adds an API endpoint for getting a channel's authors ([#242](https://github.com/wherebyus/platformservice/issues/242)) ([517b33b](https://github.com/wherebyus/platformservice/commit/517b33b))
* 🎸 Adds positioning to AdTypeService ([28143de](https://github.com/wherebyus/platformservice/commit/28143de))
* 🎸 Adds scheduled and template statuses ([a2a9b6a](https://github.com/wherebyus/platformservice/commit/a2a9b6a))
* 🎸 Adds the date query parameter to the ads api ([7593ee5](https://github.com/wherebyus/platformservice/commit/7593ee5))
* 🎸 Enables the promotion type scaffolding endpoint ([#246](https://github.com/wherebyus/platformservice/issues/246)) ([51a7502](https://github.com/wherebyus/platformservice/commit/51a7502))
* 🎸 wordmark is the banner ([#254](https://github.com/wherebyus/platformservice/issues/254)) ([a0ccc3d](https://github.com/wherebyus/platformservice/commit/a0ccc3d))
* adds a channel scaffold api ([c58d6d5](https://github.com/wherebyus/platformservice/commit/c58d6d5))

<a name="1.24.0"></a>
# [1.24.0](https://github.com/wherebyus/platformservice/compare/1.22.2...1.24.0) (2020-11-04)


### Bug Fixes

* 🐛 Adds a resolveContent parameter to the public api ([4af666c](https://github.com/wherebyus/platformservice/commit/4af666c))
* 🐛 correct private properties ([6de5848](https://github.com/wherebyus/platformservice/commit/6de5848))
* 🐛 correct sales email address ([3047198](https://github.com/wherebyus/platformservice/commit/3047198))
* 🐛 create new job to send order notification to creators ([d49d30e](https://github.com/wherebyus/platformservice/commit/d49d30e))
* 🐛 the Letter heading is no longer required ([6fcaa2c](https://github.com/wherebyus/platformservice/commit/6fcaa2c))


### Features

* 🎸 1.22.2 ([d71076b](https://github.com/wherebyus/platformservice/commit/d71076b))
* 🎸 add a byline to email template ([#243](https://github.com/wherebyus/platformservice/issues/243)) ([fbbbedb](https://github.com/wherebyus/platformservice/commit/fbbbedb))
* 🎸 Adds an API endpoint for getting a channel's authors ([#242](https://github.com/wherebyus/platformservice/issues/242)) ([517b33b](https://github.com/wherebyus/platformservice/commit/517b33b))

<a name="1.23.0"></a>
# [1.23.0](https://github.com/wherebyus/platformservice/compare/1.22.2...1.23.0) (2020-10-29)


### Bug Fixes

* 🐛 correct private properties ([6de5848](https://github.com/wherebyus/platformservice/commit/6de5848))
* 🐛 correct sales email address ([3047198](https://github.com/wherebyus/platformservice/commit/3047198))
* 🐛 create new job to send order notification to creators ([d49d30e](https://github.com/wherebyus/platformservice/commit/d49d30e))
* 🐛 the Letter heading is no longer required ([6fcaa2c](https://github.com/wherebyus/platformservice/commit/6fcaa2c))


### Features

* 🎸 1.22.2 ([d71076b](https://github.com/wherebyus/platformservice/commit/d71076b))

<a name="1.21.0"></a>
# [1.21.0](https://github.com/wherebyus/platformservice/compare/1.18.0...1.21.0) (2020-10-14)


### Features

* 🎸 Adds the Promotion Policy Url configuration ([25cc36b](https://github.com/wherebyus/platformservice/commit/25cc36b))
* 🎸 Adds the promotionPolicyUrl to the Channel model ([04d96e3](https://github.com/wherebyus/platformservice/commit/04d96e3))



<a name="1.17.0"></a>
# [1.17.0](https://github.com/wherebyus/platformservice/compare/1.16.1...1.17.0) (2020-09-28)

<a name="1.19.0"></a>
# [1.19.0](https://github.com/wherebyus/platformservice/compare/1.18.0...1.19.0) (2020-09-30)



<a name="1.17.0"></a>
# [1.17.0](https://github.com/wherebyus/platformservice/compare/1.16.1...1.17.0) (2020-09-28)

<a name="1.18.0"></a>
# [1.18.0](https://github.com/wherebyus/platformservice/compare/1.16.1...1.18.0) (2020-09-29)


### Bug Fixes

* 🐛 Adds a null-check on the SendOrderReceipt job ([91c572a](https://github.com/wherebyus/platformservice/commit/91c572a))
* 🐛 Corrects the CallToActionURL ([0c642e0](https://github.com/wherebyus/platformservice/commit/0c642e0))


### Features

* 🎸 A promoter will now be confirmed their promo is sched ([bff37a9](https://github.com/wherebyus/platformservice/commit/bff37a9))


<a name="1.17.0"></a>
# [1.17.0](https://github.com/wherebyus/platformservice/compare/1.16.1...1.17.0) (2020-09-28)


### Bug Fixes

* 🐛 Adds a null-check on the SendOrderReceipt job ([91c572a](https://github.com/wherebyus/platformservice/commit/91c572a))
* 🐛 change event names ([#199](https://github.com/wherebyus/platformservice/issues/199)) ([649fe9a](https://github.com/wherebyus/platformservice/commit/649fe9a))
* 🐛 Corrects the CallToActionURL ([0c642e0](https://github.com/wherebyus/platformservice/commit/0c642e0))
* 🐛 Removes the customer receipt ([484ba1a](https://github.com/wherebyus/platformservice/commit/484ba1a))
* 🐛 Updates some copy of this email ([a226b2f](https://github.com/wherebyus/platformservice/commit/a226b2f))
* 🐛 Uses the Event contract and removes dead code ([5eb4919](https://github.com/wherebyus/platformservice/commit/5eb4919))


### Features

* 🎸 A promoter will now be confirmed their promo is sched ([bff37a9](https://github.com/wherebyus/platformservice/commit/bff37a9))
* 🎸 update user ([#188](https://github.com/wherebyus/platformservice/issues/188)) ([c15753f](https://github.com/wherebyus/platformservice/commit/c15753f))
* 🎸 use event and listener to send receipt emails ([9af8830](https://github.com/wherebyus/platformservice/commit/9af8830))

<a name="1.16.1"></a>
## [1.16.1](https://github.com/wherebyus/platformservice/compare/1.16.0...1.16.1) (2020-09-16)


### Bug Fixes
* 🐛 Receipts trigger on free orders ([730bb76](https://github.com/wherebyus/platformservice/commit/730bb76))


### Features

* 🎸 update user ([#188](https://github.com/wherebyus/platformservice/issues/188)) ([c15753f](https://github.com/wherebyus/platformservice/commit/c15753f))

<a name="1.16.0"></a>
# [1.16.0](https://github.com/wherebyus/platformservice/compare/1.15.0...1.16.0) (2020-09-10)

### Features

<a name="1.15.0"></a>
# [1.15.0](https://github.com/wherebyus/platformservice/compare/1.14.0...1.15.0) (2020-09-01)


### Bug Fixes

* 🐛 Changes some brand object properties ([de4078d](https://github.com/wherebyus/platformservice/commit/de4078d))
* 🐛 Removes a configuration object from the Brand model ([2588d62](https://github.com/wherebyus/platformservice/commit/2588d62))


### Features

* 🎸 Adds a new endpoint to get the metrics of a channel ([50c504b](https://github.com/wherebyus/platformservice/commit/50c504b))

<a name="1.14.0"></a>
# [1.14.0](https://github.com/wherebyus/platformservice/compare/1.13.0...1.14.0) (2020-08-30)


### Bug Fixes

* 🐛 Adds all sortso of stuff ([1a7ad20](https://github.com/wherebyus/platformservice/commit/1a7ad20))

<a name="1.13.1"></a>
## [1.13.1](https://github.com/wherebyus/platformservice/compare/1.13.0...1.13.1) (2020-08-20)

<a name="1.13.0"></a>
# [1.13.0](https://github.com/wherebyus/platformservice/compare/1.12.0...1.13.0) (2020-08-19)


### Bug Fixes

* corrects route typo ([0a09355](https://github.com/wherebyus/platformservice/commit/0a09355))

<a name="1.12.0"></a>
# [1.12.0](https://github.com/wherebyus/platformservice/compare/1.11.0...1.12.0) (2020-07-30)


### Features

* 🎸 Adds a route for fetching Platforms the user manages ([6c0e243](https://github.com/wherebyus/platformservice/commit/6c0e243))

<a name="1.11.0"></a>
# [1.11.0](https://github.com/wherebyus/platformservice/compare/1.10.0...1.11.0) (2020-07-30)


### Features

* 🎸 getChannels ([#162](https://github.com/wherebyus/platformservice/issues/162)) ([9584178](https://github.com/wherebyus/platformservice/commit/9584178))

<a name="1.10.0"></a>
# [1.10.0](https://github.com/wherebyus/platformservice/compare/1.9.0...1.10.0) (2020-07-29)

<a name="1.9.0"></a>
# [1.9.0](https://github.com/wherebyus/platformservice/compare/1.8.7...1.9.0) (2020-07-29)


### Features

* 🎸 Adds dates with no scheduled content to disabledDates ([2e6a0ec](https://github.com/wherebyus/platformservice/commit/2e6a0ec))
* 🎸 creat a brand and a channel ([#161](https://github.com/wherebyus/platformservice/issues/161)) ([aa17ce1](https://github.com/wherebyus/platformservice/commit/aa17ce1))

<a name="1.8.7"></a>
## [1.8.7](https://github.com/wherebyus/platformservice/compare/1.8.6...1.8.7) (2020-07-09)


### Bug Fixes

* 🐛 remove commented code ([787844c](https://github.com/wherebyus/platformservice/commit/787844c))
* 🐛 type hint parameter, correct file request methods ([2129006](https://github.com/wherebyus/platformservice/commit/2129006))

<a name="1.8.6"></a>
## [1.8.6](https://github.com/wherebyus/platformservice/compare/1.8.5...1.8.6) (2020-07-07)

<a name="1.8.5"></a>
## [1.8.5](https://github.com/wherebyus/platformservice/compare/1.8.4...1.8.5) (2020-07-06)


### Bug Fixes

* 🐛 Corrects typo in ValidateChannelMiddleware ([cd3d228](https://github.com/wherebyus/platformservice/commit/cd3d228))

<a name="1.8.4"></a>
## [1.8.4](https://github.com/wherebyus/platformservice/compare/1.8.3...1.8.4) (2020-07-06)


### Bug Fixes

* add .DS_Store to gitignore ([19a5980](https://github.com/wherebyus/platformservice/commit/19a5980))
* correct test name ([7e46157](https://github.com/wherebyus/platformservice/commit/7e46157))

<a name="1.8.3"></a>
## [1.8.3](https://github.com/wherebyus/platformservice/compare/1.8.2...1.8.3) (2020-07-02)


### Bug Fixes

* spacing ([b577a27](https://github.com/wherebyus/platformservice/commit/b577a27))

<a name="1.8.2"></a>
## [1.8.2](https://github.com/wherebyus/platformservice/compare/1.8.1...1.8.2) (2020-07-01)


### Bug Fixes

* 🐛 Channel middleware now checks that channel belongs to ([#112](https://github.com/wherebyus/platformservice/issues/112)) ([2178b5b](https://github.com/wherebyus/platformservice/commit/2178b5b))

<a name="1.8.1"></a>
## [1.8.1](https://github.com/wherebyus/platformservice/compare/1.8.0...1.8.1) (2020-07-01)

<a name="1.8.0"></a>
# [1.8.0](https://github.com/wherebyus/platformservice/compare/1.7.2...1.8.0) (2020-06-30)


### Bug Fixes

* remove dead param ([1c85c15](https://github.com/wherebyus/platformservice/commit/1c85c15))


### Features

* 🎸 add API endpoint for disabled dates by ad types ([57fc789](https://github.com/wherebyus/platformservice/commit/57fc789))

<a name="1.7.2"></a>
## [1.7.2](https://github.com/wherebyus/platformservice/compare/1.7.1...1.7.2) (2020-06-25)


### Bug Fixes

* 🐛 channel Images have unique names when create and update ([#96](https://github.com/wherebyus/platformservice/issues/96)) ([c6ade54](https://github.com/wherebyus/platformservice/commit/c6ade54))

<a name="1.7.1"></a>
## [1.7.1](https://github.com/wherebyus/platformservice/compare/1.6.0...1.7.1) (2020-06-23)


### Bug Fixes

* 🐛 channelImage can't be 'null' ([#88](https://github.com/wherebyus/platformservice/issues/88)) ([6fa21d1](https://github.com/wherebyus/platformservice/commit/6fa21d1))
* 🐛 correct pathing typos ([7d60cb5](https://github.com/wherebyus/platformservice/commit/7d60cb5))
* adds an options route to the ads API. ([2fe8bb6](https://github.com/wherebyus/platformservice/commit/2fe8bb6))


### Features

* 🎸 adds a content to the AdService field listener ([#84](https://github.com/wherebyus/platformservice/issues/84)) ([047289d](https://github.com/wherebyus/platformservice/commit/047289d))

<a name="1.7.0"></a>
# [1.7.0](https://github.com/wherebyus/platformservice/compare/1.6.0...1.7.0) (2020-06-17)


### Bug Fixes

* adds an options route to the ads API. ([2fe8bb6](https://github.com/wherebyus/platformservice/commit/2fe8bb6))


### Features

* 🎸 adds a content to the AdService field listener ([#84](https://github.com/wherebyus/platformservice/issues/84)) ([047289d](https://github.com/wherebyus/platformservice/commit/047289d))

<a name="1.6.0"></a>
# [1.6.0](https://github.com/wherebyus/platformservice/compare/1.5.1...1.6.0) (2020-06-17)


### Bug Fixes

* 🐛 adds an options rout to the ads restful resource ([2ba679a](https://github.com/wherebyus/platformservice/commit/2ba679a))
* 🐛 adds the Delete method to our CORS handler ([7cf75cd](https://github.com/wherebyus/platformservice/commit/7cf75cd))


### Features

* 🎸 Ad Type creation now accepts a template ([6f9064d](https://github.com/wherebyus/platformservice/commit/6f9064d))
* 🎸 Adds displayHidden query parameter to GetPackages ([#64](https://github.com/wherebyus/platformservice/issues/64)) ([e441fa6](https://github.com/wherebyus/platformservice/commit/e441fa6))
* 🎸 adds new orders route ([2a4b1a3](https://github.com/wherebyus/platformservice/commit/2a4b1a3))
* 🎸 delete ad method and route ([#82](https://github.com/wherebyus/platformservice/issues/82)) ([5dbbbc1](https://github.com/wherebyus/platformservice/commit/5dbbbc1))


* Staging (#62) ([026c4d7](https://github.com/wherebyus/platformservice/commit/026c4d7)), closes [#62](https://github.com/wherebyus/platformservice/issues/62) [#32](https://github.com/wherebyus/platformservice/issues/32) [#33](https://github.com/wherebyus/platformservice/issues/33) [#34](https://github.com/wherebyus/platformservice/issues/34) [#36](https://github.com/wherebyus/platformservice/issues/36) [#141](https://github.com/wherebyus/platformservice/issues/141) [#38](https://github.com/wherebyus/platformservice/issues/38) [#40](https://github.com/wherebyus/platformservice/issues/40) [#41](https://github.com/wherebyus/platformservice/issues/41) [#42](https://github.com/wherebyus/platformservice/issues/42) [#43](https://github.com/wherebyus/platformservice/issues/43) [#45](https://github.com/wherebyus/platformservice/issues/45) [#52](https://github.com/wherebyus/platformservice/issues/52) [#55](https://github.com/wherebyus/platformservice/issues/55) [#57](https://github.com/wherebyus/platformservice/issues/57) [#58](https://github.com/wherebyus/platformservice/issues/58) [#59](https://github.com/wherebyus/platformservice/issues/59) [#61](https://github.com/wherebyus/platformservice/issues/61) [#51](https://github.com/wherebyus/platformservice/issues/51) [#32](https://github.com/wherebyus/platformservice/issues/32) [#33](https://github.com/wherebyus/platformservice/issues/33) [#34](https://github.com/wherebyus/platformservice/issues/34) [#36](https://github.com/wherebyus/platformservice/issues/36) [#141](https://github.com/wherebyus/platformservice/issues/141) [#38](https://github.com/wherebyus/platformservice/issues/38) [#40](https://github.com/wherebyus/platformservice/issues/40) [#41](https://github.com/wherebyus/platformservice/issues/41) [#42](https://github.com/wherebyus/platformservice/issues/42) [#43](https://github.com/wherebyus/platformservice/issues/43) [#45](https://github.com/wherebyus/platformservice/issues/45) [#56](https://github.com/wherebyus/platformservice/issues/56) [#32](https://github.com/wherebyus/platformservice/issues/32) [#33](https://github.com/wherebyus/platformservice/issues/33) [#34](https://github.com/wherebyus/platformservice/issues/34) [#36](https://github.com/wherebyus/platformservice/issues/36) [#141](https://github.com/wherebyus/platformservice/issues/141) [#38](https://github.com/wherebyus/platformservice/issues/38) [#40](https://github.com/wherebyus/platformservice/issues/40) [#41](https://github.com/wherebyus/platformservice/issues/41) [#42](https://github.com/wherebyus/platformservice/issues/42) [#43](https://github.com/wherebyus/platformservice/issues/43) [#45](https://github.com/wherebyus/platformservice/issues/45) [#52](https://github.com/wherebyus/platformservice/issues/52) [#55](https://github.com/wherebyus/platformservice/issues/55)


### BREAKING CHANGES

* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.

* refactor: Changes the BrandConfigurationCollection

* feat: adds BrandUrl to the brand public object
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.

* refactor: Changes the BrandConfigurationCollection

* feat: adds BrandUrl to the brand public object
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.

* refactor: Changes the BrandConfigurationCollection

* feat: adds BrandUrl to the brand public object

<a name="1.5.2"></a>
## [1.5.2](https://github.com/wherebyus/platformservice/compare/1.5.1...1.5.2) (2020-06-08)


### Features

* 🎸 Ad Type creation now accepts a template ([6f9064d](https://github.com/wherebyus/platformservice/commit/6f9064d))
* 🎸 Adds displayHidden query parameter to GetPackages ([#64](https://github.com/wherebyus/platformservice/issues/64)) ([e441fa6](https://github.com/wherebyus/platformservice/commit/e441fa6))


* Staging (#62) ([026c4d7](https://github.com/wherebyus/platformservice/commit/026c4d7)), closes [#62](https://github.com/wherebyus/platformservice/issues/62) [#32](https://github.com/wherebyus/platformservice/issues/32) [#33](https://github.com/wherebyus/platformservice/issues/33) [#34](https://github.com/wherebyus/platformservice/issues/34) [#36](https://github.com/wherebyus/platformservice/issues/36) [#141](https://github.com/wherebyus/platformservice/issues/141) [#38](https://github.com/wherebyus/platformservice/issues/38) [#40](https://github.com/wherebyus/platformservice/issues/40) [#41](https://github.com/wherebyus/platformservice/issues/41) [#42](https://github.com/wherebyus/platformservice/issues/42) [#43](https://github.com/wherebyus/platformservice/issues/43) [#45](https://github.com/wherebyus/platformservice/issues/45) [#52](https://github.com/wherebyus/platformservice/issues/52) [#55](https://github.com/wherebyus/platformservice/issues/55) [#57](https://github.com/wherebyus/platformservice/issues/57) [#58](https://github.com/wherebyus/platformservice/issues/58) [#59](https://github.com/wherebyus/platformservice/issues/59) [#61](https://github.com/wherebyus/platformservice/issues/61) [#51](https://github.com/wherebyus/platformservice/issues/51) [#32](https://github.com/wherebyus/platformservice/issues/32) [#33](https://github.com/wherebyus/platformservice/issues/33) [#34](https://github.com/wherebyus/platformservice/issues/34) [#36](https://github.com/wherebyus/platformservice/issues/36) [#141](https://github.com/wherebyus/platformservice/issues/141) [#38](https://github.com/wherebyus/platformservice/issues/38) [#40](https://github.com/wherebyus/platformservice/issues/40) [#41](https://github.com/wherebyus/platformservice/issues/41) [#42](https://github.com/wherebyus/platformservice/issues/42) [#43](https://github.com/wherebyus/platformservice/issues/43) [#45](https://github.com/wherebyus/platformservice/issues/45) [#56](https://github.com/wherebyus/platformservice/issues/56) [#32](https://github.com/wherebyus/platformservice/issues/32) [#33](https://github.com/wherebyus/platformservice/issues/33) [#34](https://github.com/wherebyus/platformservice/issues/34) [#36](https://github.com/wherebyus/platformservice/issues/36) [#141](https://github.com/wherebyus/platformservice/issues/141) [#38](https://github.com/wherebyus/platformservice/issues/38) [#40](https://github.com/wherebyus/platformservice/issues/40) [#41](https://github.com/wherebyus/platformservice/issues/41) [#42](https://github.com/wherebyus/platformservice/issues/42) [#43](https://github.com/wherebyus/platformservice/issues/43) [#45](https://github.com/wherebyus/platformservice/issues/45) [#52](https://github.com/wherebyus/platformservice/issues/52) [#55](https://github.com/wherebyus/platformservice/issues/55)


### BREAKING CHANGES

* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.

* refactor: Changes the BrandConfigurationCollection

* feat: adds BrandUrl to the brand public object
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.

* refactor: Changes the BrandConfigurationCollection

* feat: adds BrandUrl to the brand public object
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.

* refactor: Changes the BrandConfigurationCollection

* feat: adds BrandUrl to the brand public object

<a name="1.5.1"></a>
## [1.5.1](https://github.com/wherebyus/platformservice/compare/1.5.0...1.5.1) (2020-06-03)


### Bug Fixes

* adds missing date query parameter to the api ([0e05635](https://github.com/wherebyus/platformservice/commit/0e05635))
* correctstypo ([d3f0694](https://github.com/wherebyus/platformservice/commit/d3f0694))


### Features

* 🎸 Adds an API endpoint to update an ad ([12450aa](https://github.com/wherebyus/platformservice/commit/12450aa))
* Adds a new route to get AdTypes by Channel ([741ff36](https://github.com/wherebyus/platformservice/commit/741ff36))
* adds new promotions/types route ([76f3204](https://github.com/wherebyus/platformservice/commit/76f3204))


* Issue 3000 (#61) ([2e8a356](https://github.com/wherebyus/platformservice/commit/2e8a356)), closes [#61](https://github.com/wherebyus/platformservice/issues/61) [#51](https://github.com/wherebyus/platformservice/issues/51) [#32](https://github.com/wherebyus/platformservice/issues/32) [#33](https://github.com/wherebyus/platformservice/issues/33) [#34](https://github.com/wherebyus/platformservice/issues/34) [#36](https://github.com/wherebyus/platformservice/issues/36) [#141](https://github.com/wherebyus/platformservice/issues/141) [#38](https://github.com/wherebyus/platformservice/issues/38) [#40](https://github.com/wherebyus/platformservice/issues/40) [#41](https://github.com/wherebyus/platformservice/issues/41) [#42](https://github.com/wherebyus/platformservice/issues/42) [#43](https://github.com/wherebyus/platformservice/issues/43) [#45](https://github.com/wherebyus/platformservice/issues/45) [#56](https://github.com/wherebyus/platformservice/issues/56) [#32](https://github.com/wherebyus/platformservice/issues/32) [#33](https://github.com/wherebyus/platformservice/issues/33) [#34](https://github.com/wherebyus/platformservice/issues/34) [#36](https://github.com/wherebyus/platformservice/issues/36) [#141](https://github.com/wherebyus/platformservice/issues/141) [#38](https://github.com/wherebyus/platformservice/issues/38) [#40](https://github.com/wherebyus/platformservice/issues/40) [#41](https://github.com/wherebyus/platformservice/issues/41) [#42](https://github.com/wherebyus/platformservice/issues/42) [#43](https://github.com/wherebyus/platformservice/issues/43) [#45](https://github.com/wherebyus/platformservice/issues/45) [#52](https://github.com/wherebyus/platformservice/issues/52) [#55](https://github.com/wherebyus/platformservice/issues/55)
* release 1.5.0 (#60) ([a7c03ce](https://github.com/wherebyus/platformservice/commit/a7c03ce)), closes [#60](https://github.com/wherebyus/platformservice/issues/60) [#32](https://github.com/wherebyus/platformservice/issues/32) [#33](https://github.com/wherebyus/platformservice/issues/33) [#34](https://github.com/wherebyus/platformservice/issues/34) [#36](https://github.com/wherebyus/platformservice/issues/36) [#141](https://github.com/wherebyus/platformservice/issues/141) [#38](https://github.com/wherebyus/platformservice/issues/38) [#40](https://github.com/wherebyus/platformservice/issues/40) [#41](https://github.com/wherebyus/platformservice/issues/41) [#42](https://github.com/wherebyus/platformservice/issues/42) [#43](https://github.com/wherebyus/platformservice/issues/43) [#45](https://github.com/wherebyus/platformservice/issues/45) [#52](https://github.com/wherebyus/platformservice/issues/52) [#55](https://github.com/wherebyus/platformservice/issues/55) [#57](https://github.com/wherebyus/platformservice/issues/57) [#58](https://github.com/wherebyus/platformservice/issues/58) [#59](https://github.com/wherebyus/platformservice/issues/59)


### BREAKING CHANGES

* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.

* refactor: Changes the BrandConfigurationCollection

* feat: adds BrandUrl to the brand public object
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.

* refactor: Changes the BrandConfigurationCollection

* feat: adds BrandUrl to the brand public object
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.

* refactor: Changes the BrandConfigurationCollection

* feat: adds BrandUrl to the brand public object

<a name="1.5.0"></a>
# [1.5.0](https://github.com/wherebyus/platformservice/compare/1.1.0...1.5.0) (2020-06-02)


### Bug Fixes

* [#141](https://github.com/wherebyus/platformservice/issues/141) ([11ef459](https://github.com/wherebyus/platformservice/commit/11ef459))
* 🐛 Adds an OPTIONS wildcare to the Brand API ([#58](https://github.com/wherebyus/platformservice/issues/58)) ([cdb87b0](https://github.com/wherebyus/platformservice/commit/cdb87b0))
* 🐛 Changes the column type of two Configuration columns ([#48](https://github.com/wherebyus/platformservice/issues/48)) ([e4f3571](https://github.com/wherebyus/platformservice/commit/e4f3571))
* 🐛 Corrects issue where BrandConfigurations were being set ([57aeb1e](https://github.com/wherebyus/platformservice/commit/57aeb1e))
* 🐛 corrects wrong AdService API endpoint ([b7c27c2](https://github.com/wherebyus/platformservice/commit/b7c27c2))
* 🐛 removes Features, which we odn't use ([8cebd53](https://github.com/wherebyus/platformservice/commit/8cebd53))
* 🐛 Removes the boolean typecasts on the AdTypeService ([93b4f60](https://github.com/wherebyus/platformservice/commit/93b4f60))
* 🐛 Removes the boolean typecasts on the AdTypeService ([#59](https://github.com/wherebyus/platformservice/issues/59)) ([abdb89c](https://github.com/wherebyus/platformservice/commit/abdb89c))


* release 1.4.1 (#56) ([b0f30cd](https://github.com/wherebyus/platformservice/commit/b0f30cd)), closes [#56](https://github.com/wherebyus/platformservice/issues/56) [#32](https://github.com/wherebyus/platformservice/issues/32) [#33](https://github.com/wherebyus/platformservice/issues/33) [#34](https://github.com/wherebyus/platformservice/issues/34) [#36](https://github.com/wherebyus/platformservice/issues/36) [#141](https://github.com/wherebyus/platformservice/issues/141) [#38](https://github.com/wherebyus/platformservice/issues/38) [#40](https://github.com/wherebyus/platformservice/issues/40) [#41](https://github.com/wherebyus/platformservice/issues/41) [#42](https://github.com/wherebyus/platformservice/issues/42) [#43](https://github.com/wherebyus/platformservice/issues/43) [#45](https://github.com/wherebyus/platformservice/issues/45) [#52](https://github.com/wherebyus/platformservice/issues/52) [#55](https://github.com/wherebyus/platformservice/issues/55)
* release 1.4.0 (#51) ([d2edada](https://github.com/wherebyus/platformservice/commit/d2edada)), closes [#51](https://github.com/wherebyus/platformservice/issues/51) [#32](https://github.com/wherebyus/platformservice/issues/32) [#33](https://github.com/wherebyus/platformservice/issues/33) [#34](https://github.com/wherebyus/platformservice/issues/34) [#36](https://github.com/wherebyus/platformservice/issues/36) [#141](https://github.com/wherebyus/platformservice/issues/141) [#38](https://github.com/wherebyus/platformservice/issues/38) [#40](https://github.com/wherebyus/platformservice/issues/40) [#41](https://github.com/wherebyus/platformservice/issues/41) [#42](https://github.com/wherebyus/platformservice/issues/42) [#43](https://github.com/wherebyus/platformservice/issues/43) [#45](https://github.com/wherebyus/platformservice/issues/45)
* release 1.3.0 (#47) ([3c408e4](https://github.com/wherebyus/platformservice/commit/3c408e4)), closes [#47](https://github.com/wherebyus/platformservice/issues/47) [#32](https://github.com/wherebyus/platformservice/issues/32) [#33](https://github.com/wherebyus/platformservice/issues/33) [#34](https://github.com/wherebyus/platformservice/issues/34) [#36](https://github.com/wherebyus/platformservice/issues/36) [#141](https://github.com/wherebyus/platformservice/issues/141) [#38](https://github.com/wherebyus/platformservice/issues/38) [#40](https://github.com/wherebyus/platformservice/issues/40) [#41](https://github.com/wherebyus/platformservice/issues/41) [#42](https://github.com/wherebyus/platformservice/issues/42) [#43](https://github.com/wherebyus/platformservice/issues/43)
* release 1.2.0 (#44) ([15d950f](https://github.com/wherebyus/platformservice/commit/15d950f)), closes [#44](https://github.com/wherebyus/platformservice/issues/44) [#32](https://github.com/wherebyus/platformservice/issues/32) [#33](https://github.com/wherebyus/platformservice/issues/33) [#34](https://github.com/wherebyus/platformservice/issues/34) [#36](https://github.com/wherebyus/platformservice/issues/36) [#141](https://github.com/wherebyus/platformservice/issues/141) [#38](https://github.com/wherebyus/platformservice/issues/38) [#40](https://github.com/wherebyus/platformservice/issues/40) [#41](https://github.com/wherebyus/platformservice/issues/41) [#42](https://github.com/wherebyus/platformservice/issues/42) [#43](https://github.com/wherebyus/platformservice/issues/43)
* Release 1.1.1 (#39) ([123773b](https://github.com/wherebyus/platformservice/commit/123773b)), closes [#39](https://github.com/wherebyus/platformservice/issues/39) [#32](https://github.com/wherebyus/platformservice/issues/32) [#33](https://github.com/wherebyus/platformservice/issues/33) [#34](https://github.com/wherebyus/platformservice/issues/34) [#36](https://github.com/wherebyus/platformservice/issues/36) [#141](https://github.com/wherebyus/platformservice/issues/141) [#38](https://github.com/wherebyus/platformservice/issues/38)
* release 1.0.1 (#37) ([5e84352](https://github.com/wherebyus/platformservice/commit/5e84352)), closes [#37](https://github.com/wherebyus/platformservice/issues/37) [#32](https://github.com/wherebyus/platformservice/issues/32) [#33](https://github.com/wherebyus/platformservice/issues/33) [#34](https://github.com/wherebyus/platformservice/issues/34) [#36](https://github.com/wherebyus/platformservice/issues/36) [#141](https://github.com/wherebyus/platformservice/issues/141)


### Features

* 🎸 ad types can now be deleted through platformservice ([54dc228](https://github.com/wherebyus/platformservice/commit/54dc228))
* 🎸 adds new optional properties to the Ads api ([0f04b6b](https://github.com/wherebyus/platformservice/commit/0f04b6b))
* 🎸 We can now delete a package over the API ([#40](https://github.com/wherebyus/platformservice/issues/40)) ([a49dbf3](https://github.com/wherebyus/platformservice/commit/a49dbf3))


### BREAKING CHANGES

* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.

* refactor: Changes the BrandConfigurationCollection

* feat: adds BrandUrl to the brand public object
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.

* refactor: Changes the BrandConfigurationCollection

* feat: adds BrandUrl to the brand public object
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.

* refactor: Changes the BrandConfigurationCollection

* feat: adds BrandUrl to the brand public object
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.

* refactor: Changes the BrandConfigurationCollection

* feat: adds BrandUrl to the brand public object
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.

* refactor: Changes the BrandConfigurationCollection

* feat: adds BrandUrl to the brand public object
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.

* refactor: Changes the BrandConfigurationCollection

* feat: adds BrandUrl to the brand public object

<a name="1.4.1"></a>
## [1.4.1](https://github.com/wherebyus/platformservice/compare/1.1.0...1.4.1) (2020-05-29)


### Bug Fixes

* [#141](https://github.com/wherebyus/platformservice/issues/141) ([11ef459](https://github.com/wherebyus/platformservice/commit/11ef459))
* 🐛 Changes the column type of two Configuration columns ([#48](https://github.com/wherebyus/platformservice/issues/48)) ([e4f3571](https://github.com/wherebyus/platformservice/commit/e4f3571))
* 🐛 Corrects issue where BrandConfigurations were being set ([57aeb1e](https://github.com/wherebyus/platformservice/commit/57aeb1e))
* 🐛 corrects wrong AdService API endpoint ([b7c27c2](https://github.com/wherebyus/platformservice/commit/b7c27c2))
* 🐛 removes Features, which we odn't use ([8cebd53](https://github.com/wherebyus/platformservice/commit/8cebd53))


### Features

* 🎸 ad types can now be deleted through platformservice ([54dc228](https://github.com/wherebyus/platformservice/commit/54dc228))
* 🎸 adds new optional properties to the Ads api ([0f04b6b](https://github.com/wherebyus/platformservice/commit/0f04b6b))
* 🎸 We can now delete a package over the API ([#40](https://github.com/wherebyus/platformservice/issues/40)) ([a49dbf3](https://github.com/wherebyus/platformservice/commit/a49dbf3))


### Features

* 🎸 ad types can now be deleted through platformservice ([54dc228](https://github.com/wherebyus/platformservice/commit/54dc228))
* 🎸 We can now delete a package over the API ([#40](https://github.com/wherebyus/platformservice/issues/40)) ([a49dbf3](https://github.com/wherebyus/platformservice/commit/a49dbf3))


### BREAKING CHANGES

* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.

* refactor: Changes the BrandConfigurationCollection

* feat: adds BrandUrl to the brand public object
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.

* refactor: Changes the BrandConfigurationCollection

* feat: adds BrandUrl to the brand public object
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.

* refactor: Changes the BrandConfigurationCollection

* feat: adds BrandUrl to the brand public object
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.

* refactor: Changes the BrandConfigurationCollection

* feat: adds BrandUrl to the brand public object

<a name="1.3.2"></a>
## [1.3.2](https://github.com/wherebyus/platformservice/compare/0.22.0...1.3.2) (2020-05-26)


### Bug Fixes

* [#141](https://github.com/wherebyus/platformservice/issues/141) ([11ef459](https://github.com/wherebyus/platformservice/commit/11ef459))
* 🐛 Changes the column type of two Configuration columns ([#48](https://github.com/wherebyus/platformservice/issues/48)) ([e4f3571](https://github.com/wherebyus/platformservice/commit/e4f3571))
* 🐛 Corrects issue where BrandConfigurations were being set ([57aeb1e](https://github.com/wherebyus/platformservice/commit/57aeb1e))
* 🐛 Corrects issue with empty BrandConfigurations ([6347c62](https://github.com/wherebyus/platformservice/commit/6347c62))
* 🐛 Corrects issues with pre-existing url configs and nulls ([#49](https://github.com/wherebyus/platformservice/issues/49)) ([a95c173](https://github.com/wherebyus/platformservice/commit/a95c173))
* 🐛 corrects revenueShare and stripe account not being found ([006a08d](https://github.com/wherebyus/platformservice/commit/006a08d))
* 🐛 removes Features, which we odn't use ([8cebd53](https://github.com/wherebyus/platformservice/commit/8cebd53))
* ads missing variable to Pipedrive Order Zap ([38275fb](https://github.com/wherebyus/platformservice/commit/38275fb))


* release 1.3.0 (#47) ([3c408e4](https://github.com/wherebyus/platformservice/commit/3c408e4)), closes [#47](https://github.com/wherebyus/platformservice/issues/47) [#32](https://github.com/wherebyus/platformservice/issues/32) [#33](https://github.com/wherebyus/platformservice/issues/33) [#34](https://github.com/wherebyus/platformservice/issues/34) [#36](https://github.com/wherebyus/platformservice/issues/36) [#141](https://github.com/wherebyus/platformservice/issues/141) [#38](https://github.com/wherebyus/platformservice/issues/38) [#40](https://github.com/wherebyus/platformservice/issues/40) [#41](https://github.com/wherebyus/platformservice/issues/41) [#42](https://github.com/wherebyus/platformservice/issues/42) [#43](https://github.com/wherebyus/platformservice/issues/43)
* release 1.2.0 (#44) ([15d950f](https://github.com/wherebyus/platformservice/commit/15d950f)), closes [#44](https://github.com/wherebyus/platformservice/issues/44) [#32](https://github.com/wherebyus/platformservice/issues/32) [#33](https://github.com/wherebyus/platformservice/issues/33) [#34](https://github.com/wherebyus/platformservice/issues/34) [#36](https://github.com/wherebyus/platformservice/issues/36) [#141](https://github.com/wherebyus/platformservice/issues/141) [#38](https://github.com/wherebyus/platformservice/issues/38) [#40](https://github.com/wherebyus/platformservice/issues/40) [#41](https://github.com/wherebyus/platformservice/issues/41) [#42](https://github.com/wherebyus/platformservice/issues/42) [#43](https://github.com/wherebyus/platformservice/issues/43)
* Release 1.1.1 (#39) ([123773b](https://github.com/wherebyus/platformservice/commit/123773b)), closes [#39](https://github.com/wherebyus/platformservice/issues/39) [#32](https://github.com/wherebyus/platformservice/issues/32) [#33](https://github.com/wherebyus/platformservice/issues/33) [#34](https://github.com/wherebyus/platformservice/issues/34) [#36](https://github.com/wherebyus/platformservice/issues/36) [#141](https://github.com/wherebyus/platformservice/issues/141) [#38](https://github.com/wherebyus/platformservice/issues/38)
* release 1.0.1 (#37) ([5e84352](https://github.com/wherebyus/platformservice/commit/5e84352)), closes [#37](https://github.com/wherebyus/platformservice/issues/37) [#32](https://github.com/wherebyus/platformservice/issues/32) [#33](https://github.com/wherebyus/platformservice/issues/33) [#34](https://github.com/wherebyus/platformservice/issues/34) [#36](https://github.com/wherebyus/platformservice/issues/36) [#141](https://github.com/wherebyus/platformservice/issues/141)
* Release 1.0.0 (#35) ([cefbbc5](https://github.com/wherebyus/platformservice/commit/cefbbc5)), closes [#35](https://github.com/wherebyus/platformservice/issues/35) [#32](https://github.com/wherebyus/platformservice/issues/32) [#33](https://github.com/wherebyus/platformservice/issues/33) [#34](https://github.com/wherebyus/platformservice/issues/34)


### BREAKING CHANGES

* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.

* refactor: Changes the BrandConfigurationCollection

* feat: adds BrandUrl to the brand public object
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.

* refactor: Changes the BrandConfigurationCollection

* feat: adds BrandUrl to the brand public object
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.

* refactor: Changes the BrandConfigurationCollection

* feat: adds BrandUrl to the brand public object
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.

* refactor: Changes the BrandConfigurationCollection

* feat: adds BrandUrl to the brand public object
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.

* refactor: Changes the BrandConfigurationCollection

* feat: adds BrandUrl to the brand public object

* chore: adds a major release script

* Release 1.0.0

Co-authored-by: Jun Su <55222951+jujopico@users.noreply.github.com>

<a name="1.3.1"></a>
## [1.3.1](https://github.com/wherebyus/platformservice/compare/0.22.0...1.3.1) (2020-05-26)


### Bug Fixes

* [#141](https://github.com/wherebyus/platformservice/issues/141) ([11ef459](https://github.com/wherebyus/platformservice/commit/11ef459))
* 🐛 Changes the column type of two Configuration columns ([624cd43](https://github.com/wherebyus/platformservice/commit/624cd43))
* 🐛 Corrects issue where BrandConfigurations were being set ([57aeb1e](https://github.com/wherebyus/platformservice/commit/57aeb1e))
* 🐛 Corrects issue with empty BrandConfigurations ([6347c62](https://github.com/wherebyus/platformservice/commit/6347c62))
* 🐛 corrects revenueShare and stripe account not being found ([006a08d](https://github.com/wherebyus/platformservice/commit/006a08d))
* 🐛 removes Features, which we odn't use ([8cebd53](https://github.com/wherebyus/platformservice/commit/8cebd53))
* ads missing variable to Pipedrive Order Zap ([38275fb](https://github.com/wherebyus/platformservice/commit/38275fb))


* release 1.3.0 (#47) ([3c408e4](https://github.com/wherebyus/platformservice/commit/3c408e4)), closes [#47](https://github.com/wherebyus/platformservice/issues/47) [#32](https://github.com/wherebyus/platformservice/issues/32) [#33](https://github.com/wherebyus/platformservice/issues/33) [#34](https://github.com/wherebyus/platformservice/issues/34) [#36](https://github.com/wherebyus/platformservice/issues/36) [#141](https://github.com/wherebyus/platformservice/issues/141) [#38](https://github.com/wherebyus/platformservice/issues/38) [#40](https://github.com/wherebyus/platformservice/issues/40) [#41](https://github.com/wherebyus/platformservice/issues/41) [#42](https://github.com/wherebyus/platformservice/issues/42) [#43](https://github.com/wherebyus/platformservice/issues/43)
* release 1.2.0 (#44) ([15d950f](https://github.com/wherebyus/platformservice/commit/15d950f)), closes [#44](https://github.com/wherebyus/platformservice/issues/44) [#32](https://github.com/wherebyus/platformservice/issues/32) [#33](https://github.com/wherebyus/platformservice/issues/33) [#34](https://github.com/wherebyus/platformservice/issues/34) [#36](https://github.com/wherebyus/platformservice/issues/36) [#141](https://github.com/wherebyus/platformservice/issues/141) [#38](https://github.com/wherebyus/platformservice/issues/38) [#40](https://github.com/wherebyus/platformservice/issues/40) [#41](https://github.com/wherebyus/platformservice/issues/41) [#42](https://github.com/wherebyus/platformservice/issues/42) [#43](https://github.com/wherebyus/platformservice/issues/43)
* Release 1.1.1 (#39) ([123773b](https://github.com/wherebyus/platformservice/commit/123773b)), closes [#39](https://github.com/wherebyus/platformservice/issues/39) [#32](https://github.com/wherebyus/platformservice/issues/32) [#33](https://github.com/wherebyus/platformservice/issues/33) [#34](https://github.com/wherebyus/platformservice/issues/34) [#36](https://github.com/wherebyus/platformservice/issues/36) [#141](https://github.com/wherebyus/platformservice/issues/141) [#38](https://github.com/wherebyus/platformservice/issues/38)
* release 1.0.1 (#37) ([5e84352](https://github.com/wherebyus/platformservice/commit/5e84352)), closes [#37](https://github.com/wherebyus/platformservice/issues/37) [#32](https://github.com/wherebyus/platformservice/issues/32) [#33](https://github.com/wherebyus/platformservice/issues/33) [#34](https://github.com/wherebyus/platformservice/issues/34) [#36](https://github.com/wherebyus/platformservice/issues/36) [#141](https://github.com/wherebyus/platformservice/issues/141)
* Release 1.0.0 (#35) ([cefbbc5](https://github.com/wherebyus/platformservice/commit/cefbbc5)), closes [#35](https://github.com/wherebyus/platformservice/issues/35) [#32](https://github.com/wherebyus/platformservice/issues/32) [#33](https://github.com/wherebyus/platformservice/issues/33) [#34](https://github.com/wherebyus/platformservice/issues/34)


### BREAKING CHANGES

* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.

* refactor: Changes the BrandConfigurationCollection

* feat: adds BrandUrl to the brand public object
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.

* refactor: Changes the BrandConfigurationCollection

* feat: adds BrandUrl to the brand public object
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.

* refactor: Changes the BrandConfigurationCollection

* feat: adds BrandUrl to the brand public object
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.

* refactor: Changes the BrandConfigurationCollection

* feat: adds BrandUrl to the brand public object
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.

* refactor: Changes the BrandConfigurationCollection

* feat: adds BrandUrl to the brand public object

* chore: adds a major release script

* Release 1.0.0

Co-authored-by: Jun Su <55222951+jujopico@users.noreply.github.com>

<a name="1.3.0"></a>
# [1.3.0](https://github.com/wherebyus/platformservice/compare/1.1.0...1.3.0) (2020-05-21)


### Bug Fixes

* [#141](https://github.com/wherebyus/platformservice/issues/141) ([11ef459](https://github.com/wherebyus/platformservice/commit/11ef459))
* 🐛 Corrects issue where BrandConfigurations were being set ([57aeb1e](https://github.com/wherebyus/platformservice/commit/57aeb1e))
* 🐛 corrects wrong AdService API endpoint ([b7c27c2](https://github.com/wherebyus/platformservice/commit/b7c27c2))
* 🐛 removes Features, which we odn't use ([8cebd53](https://github.com/wherebyus/platformservice/commit/8cebd53))


### Features

* 🎸 ad types can now be deleted through platformservice ([54dc228](https://github.com/wherebyus/platformservice/commit/54dc228))
* 🎸 We can now delete a package over the API ([#40](https://github.com/wherebyus/platformservice/issues/40)) ([a49dbf3](https://github.com/wherebyus/platformservice/commit/a49dbf3))


* release 1.2.0 (#44) ([15d950f](https://github.com/wherebyus/platformservice/commit/15d950f)), closes [#44](https://github.com/wherebyus/platformservice/issues/44) [#32](https://github.com/wherebyus/platformservice/issues/32) [#33](https://github.com/wherebyus/platformservice/issues/33) [#34](https://github.com/wherebyus/platformservice/issues/34) [#36](https://github.com/wherebyus/platformservice/issues/36) [#141](https://github.com/wherebyus/platformservice/issues/141) [#38](https://github.com/wherebyus/platformservice/issues/38) [#40](https://github.com/wherebyus/platformservice/issues/40) [#41](https://github.com/wherebyus/platformservice/issues/41) [#42](https://github.com/wherebyus/platformservice/issues/42) [#43](https://github.com/wherebyus/platformservice/issues/43)
* Release 1.1.1 (#39) ([123773b](https://github.com/wherebyus/platformservice/commit/123773b)), closes [#39](https://github.com/wherebyus/platformservice/issues/39) [#32](https://github.com/wherebyus/platformservice/issues/32) [#33](https://github.com/wherebyus/platformservice/issues/33) [#34](https://github.com/wherebyus/platformservice/issues/34) [#36](https://github.com/wherebyus/platformservice/issues/36) [#141](https://github.com/wherebyus/platformservice/issues/141) [#38](https://github.com/wherebyus/platformservice/issues/38)
* release 1.0.1 (#37) ([5e84352](https://github.com/wherebyus/platformservice/commit/5e84352)), closes [#37](https://github.com/wherebyus/platformservice/issues/37) [#32](https://github.com/wherebyus/platformservice/issues/32) [#33](https://github.com/wherebyus/platformservice/issues/33) [#34](https://github.com/wherebyus/platformservice/issues/34) [#36](https://github.com/wherebyus/platformservice/issues/36) [#141](https://github.com/wherebyus/platformservice/issues/141)


### BREAKING CHANGES

* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.

* refactor: Changes the BrandConfigurationCollection

* feat: adds BrandUrl to the brand public object
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.

* refactor: Changes the BrandConfigurationCollection

* feat: adds BrandUrl to the brand public object
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.

* refactor: Changes the BrandConfigurationCollection

* feat: adds BrandUrl to the brand public object

<a name="1.2.0"></a>
# [1.2.0](https://github.com/wherebyus/platformservice/compare/1.1.0...1.2.0) (2020-05-20)


### Features

* 🎸 We can now delete a package over the API ([#40](https://github.com/wherebyus/platformservice/issues/40)) ([a49dbf3](https://github.com/wherebyus/platformservice/commit/a49dbf3))
<a name="1.1.1"></a>
## [1.1.1](https://github.com/wherebyus/platformservice/compare/1.1.0...1.1.1) (2020-05-17)


* release 1.0.1 (#37) ([5e84352](https://github.com/wherebyus/platformservice/commit/5e84352)), closes [#37](https://github.com/wherebyus/platformservice/issues/37) [#32](https://github.com/wherebyus/platformservice/issues/32) [#33](https://github.com/wherebyus/platformservice/issues/33) [#34](https://github.com/wherebyus/platformservice/issues/34) [#36](https://github.com/wherebyus/platformservice/issues/36) [#141](https://github.com/wherebyus/platformservice/issues/141)


### Bug Fixes

* [#141](https://github.com/wherebyus/platformservice/issues/141) ([11ef459](https://github.com/wherebyus/platformservice/commit/11ef459))
* 🐛 Corrects issue where BrandConfigurations were being set ([57aeb1e](https://github.com/wherebyus/platformservice/commit/57aeb1e))
* 🐛 removes Features, which we odn't use ([8cebd53](https://github.com/wherebyus/platformservice/commit/8cebd53))


### BREAKING CHANGES

* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.

* refactor: Changes the BrandConfigurationCollection

* feat: adds BrandUrl to the brand public object

<a name="1.1.0"></a>
# [1.1.0](https://github.com/wherebyus/platformservice/compare/0.22.0...1.1.0) (2020-05-16)

<a name="1.0.1"></a>
## [1.0.1](https://github.com/wherebyus/platformservice/compare/0.22.0...1.0.1) (2020-05-15)


### Bug Fixes

* 🐛 Corrects issue with empty BrandConfigurations ([6347c62](https://github.com/wherebyus/platformservice/commit/6347c62))
* 🐛 corrects revenueShare and stripe account not being found ([006a08d](https://github.com/wherebyus/platformservice/commit/006a08d))
* adds channelConfigurations back ([4d98f46](https://github.com/wherebyus/platformservice/commit/4d98f46))
* ads missing variable to Pipedrive Order Zap ([38275fb](https://github.com/wherebyus/platformservice/commit/38275fb))


* Release 1.0.0 (#35) ([cefbbc5](https://github.com/wherebyus/platformservice/commit/cefbbc5)), closes [#35](https://github.com/wherebyus/platformservice/issues/35) [#32](https://github.com/wherebyus/platformservice/issues/32) [#33](https://github.com/wherebyus/platformservice/issues/33) [#34](https://github.com/wherebyus/platformservice/issues/34)
* Issue 2558 (#34) ([4be769e](https://github.com/wherebyus/platformservice/commit/4be769e)), closes [#34](https://github.com/wherebyus/platformservice/issues/34)


### Documentation

* ✏️ Adds a docblock ([0b6e78e](https://github.com/wherebyus/platformservice/commit/0b6e78e))


### Features

* 🎸 adds Stripe Account and Contact Name to the Channel ([6830198](https://github.com/wherebyus/platformservice/commit/6830198))
* adds BrandUrl to the brand public object ([5c354e6](https://github.com/wherebyus/platformservice/commit/5c354e6))
* adds Contact Name to the channel array ([45e6c2c](https://github.com/wherebyus/platformservice/commit/45e6c2c))


### BREAKING CHANGES

* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.

* refactor: Changes the BrandConfigurationCollection

* feat: adds BrandUrl to the brand public object

* chore: adds a major release script

* Release 1.0.0

Co-authored-by: Jun Su <55222951+jujopico@users.noreply.github.com>
* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.
* 🧨 The channel API will now omit ChannelConfigurations

<a name="1.0.0"></a>
# [1.0.0](https://github.com/wherebyus/platformservice/compare/0.22.0...1.0.0) (2020-05-08)


### Features

* 🎸 adds Stripe Account and Contact Name to the Channel ([6830198](https://github.com/wherebyus/platformservice/commit/6830198))
* adds BrandUrl to the brand public object ([5c354e6](https://github.com/wherebyus/platformservice/commit/5c354e6))
* adds Contact Name to the channel array ([45e6c2c](https://github.com/wherebyus/platformservice/commit/45e6c2c))


* Issue 2558 (#34) ([4be769e](https://github.com/wherebyus/platformservice/commit/4be769e)), closes [#34](https://github.com/wherebyus/platformservice/issues/34)


### Bug Fixes

* adds channelConfigurations back ([4d98f46](https://github.com/wherebyus/platformservice/commit/4d98f46))
* ads missing variable to Pipedrive Order Zap ([38275fb](https://github.com/wherebyus/platformservice/commit/38275fb))


### Documentation

* ✏️ Adds a docblock ([0b6e78e](https://github.com/wherebyus/platformservice/commit/0b6e78e))


### BREAKING CHANGES

* 🧨 The channel API will now omit ChannelConfigurations

* fix: adds channelConfigurations back

* feat: 🎸 adds Stripe Account and Contact Name to the Channel

* feat: adds Contact Name to the channel array

* Removes brandId and channelId from the requet

Also, I removed the rollbar log.
* 🧨 The channel API will now omit ChannelConfigurations

<a name="0.22.0"></a>
# [0.22.0](https://github.com/wherebyus/platformservice/compare/0.21.1...0.22.0) (2020-05-01)


### Features

* 🎸 disconnect web hook ([#31](https://github.com/wherebyus/platformservice/issues/31)) ([232a194](https://github.com/wherebyus/platformservice/commit/232a194))

<a name="0.21.1"></a>
## [0.21.1](https://github.com/wherebyus/platformservice/compare/0.21.0...0.21.1) (2020-04-30)


### Bug Fixes

* a free package won't try to charge the user ([3c35319](https://github.com/wherebyus/platformservice/commit/3c35319))
* adds passport middleware back ugh ([1d7e83d](https://github.com/wherebyus/platformservice/commit/1d7e83d))
* corrects isEmpty typo ([c5fbfb3](https://github.com/wherebyus/platformservice/commit/c5fbfb3))
* passes revenue share to orders ([0777325](https://github.com/wherebyus/platformservice/commit/0777325))


### Features

* 🎸 Free packages can now be ordered ([ac778d2](https://github.com/wherebyus/platformservice/commit/ac778d2))

<a name="0.21.0"></a>
# [0.21.0](https://github.com/wherebyus/platformservice/compare/0.20.1...0.21.0) (2020-04-30)


### Features

* 🎸 adds Disabled Dates to the channel object ([40cfb67](https://github.com/wherebyus/platformservice/commit/40cfb67))

<a name="0.20.1"></a>
## [0.20.1](https://github.com/wherebyus/platformservice/compare/0.20.0...0.20.1) (2020-04-30)


### Bug Fixes

* 🐛 corrects error where a configuration shoulda been array ([0446215](https://github.com/wherebyus/platformservice/commit/0446215))
* corrects datetime format on channel deletedAt ([1fd6700](https://github.com/wherebyus/platformservice/commit/1fd6700))
* Platform will now look only for brandId from stripe ([f6d34fe](https://github.com/wherebyus/platformservice/commit/f6d34fe))

<a name="0.20.0"></a>
# [0.20.0](https://github.com/wherebyus/platformservice/compare/0.19.1...0.20.0) (2020-04-28)


### Bug Fixes

* 🐛 corrects an issue with how channel images are uploaded ([2bec456](https://github.com/wherebyus/platformservice/commit/2bec456))
* changes the channelDescription column type to longtext ([25819b6](https://github.com/wherebyus/platformservice/commit/25819b6))
* configurations can now have underscores ([68715b7](https://github.com/wherebyus/platformservice/commit/68715b7))
* corrects BrandFeatureCollection ([ba99d7c](https://github.com/wherebyus/platformservice/commit/ba99d7c))
* corrects bug with BrandFeatureCollecitons ([4082d80](https://github.com/wherebyus/platformservice/commit/4082d80))


### Features

* 🎸 adds a new channel configuration method ([b16873b](https://github.com/wherebyus/platformservice/commit/b16873b))
* adds channel images to the update route ([a5f7c06](https://github.com/wherebyus/platformservice/commit/a5f7c06))
* allows images to be uplaoded to channel configuration ([4d7f4e0](https://github.com/wherebyus/platformservice/commit/4d7f4e0))

<a name="0.19.1"></a>
## [0.19.1](https://github.com/wherebyus/platformservice/compare/0.19.0...0.19.1) (2020-04-27)


### Bug Fixes

* corrects BrandConfigurationCollection ([4bdcc21](https://github.com/wherebyus/platformservice/commit/4bdcc21))

<a name="0.19.0"></a>
# [0.19.0](https://github.com/wherebyus/platformservice/compare/0.18.0...0.19.0) (2020-04-27)


### Features

* 🎸 adds an endpoint to update a brand ([187a338](https://github.com/wherebyus/platformservice/commit/187a338))

<a name="0.18.0"></a>
# [0.18.0](https://github.com/wherebyus/platformservice/compare/0.17.0...0.18.0) (2020-04-27)


### Features

* 🎸 adds Brand and Channel configurations ([#28](https://github.com/wherebyus/platformservice/issues/28)) ([0423468](https://github.com/wherebyus/platformservice/commit/0423468))

<a name="0.17.0"></a>
# [0.17.0](https://github.com/wherebyus/platformservice/compare/0.16.0...0.17.0) (2020-04-27)


### Features

* 🎸 Channels can now be deleted ([48886c7](https://github.com/wherebyus/platformservice/commit/48886c7))
* 🎸 service platform key, its middleware and api route ([#26](https://github.com/wherebyus/platformservice/issues/26)) ([09f2afb](https://github.com/wherebyus/platformservice/commit/09f2afb))
* adds soft deletes to the channels column ([02c4875](https://github.com/wherebyus/platformservice/commit/02c4875))

<a name="0.16.0"></a>
# [0.16.0](https://github.com/wherebyus/platformservice/compare/0.15.0...0.16.0) (2020-04-22)

<a name="0.15.0"></a>
# [0.15.0](https://github.com/wherebyus/platformservice/compare/0.14.0...0.15.0) (2020-04-22)


### Features

* 🎸 adds a ton of stuff ([5431441](https://github.com/wherebyus/platformservice/commit/5431441))

<a name="0.14.0"></a>
# [0.14.0](https://github.com/wherebyus/platformservice/compare/0.13.0...0.14.0) (2020-04-20)


### Bug Fixes

* adds Cors to packages API ([cc5da36](https://github.com/wherebyus/platformservice/commit/cc5da36))


### Features

* 🎸 add ad scheduling buffer to channel configuration ([#24](https://github.com/wherebyus/platformservice/issues/24)) ([7c7cd24](https://github.com/wherebyus/platformservice/commit/7c7cd24))
* 🎸 audience based channel configuration ([#22](https://github.com/wherebyus/platformservice/issues/22)) ([ba77f14](https://github.com/wherebyus/platformservice/commit/ba77f14))

<a name="0.13.0"></a>
# [0.13.0](https://github.com/wherebyus/platformservice/compare/0.12.0...0.13.0) (2020-04-17)

<a name="0.12.0"></a>
# [0.12.0](https://github.com/wherebyus/platformservice/compare/0.11.0...0.12.0) (2020-04-15)

<a name="0.11.0"></a>
# [0.11.0](https://github.com/wherebyus/platformservice/compare/0.10.0...0.11.0) (2020-04-13)


### Features

* 🎸 fix typo ([#19](https://github.com/wherebyus/platformservice/issues/19)) ([522dc78](https://github.com/wherebyus/platformservice/commit/522dc78)), closes [#2214](https://github.com/wherebyus/platformservice/issues/2214)
* 🎸 split payment ([#18](https://github.com/wherebyus/platformservice/issues/18)) ([43b0326](https://github.com/wherebyus/platformservice/commit/43b0326)), closes [#2068](https://github.com/wherebyus/platformservice/issues/2068) [#2068](https://github.com/wherebyus/platformservice/issues/2068) [#2068](https://github.com/wherebyus/platformservice/issues/2068) [#2068](https://github.com/wherebyus/platformservice/issues/2068) [#2068](https://github.com/wherebyus/platformservice/issues/2068) [#2068](https://github.com/wherebyus/platformservice/issues/2068)

<a name="0.10.0"></a>
# [0.10.0](https://github.com/wherebyus/platformservice/compare/0.9.0...0.10.0) (2020-04-09)


### Bug Fixes

* tweaks for adservice ([fc0a438](https://github.com/wherebyus/platformservice/commit/fc0a438))


### Features

* adds ad creation controller method ([c6a358e](https://github.com/wherebyus/platformservice/commit/c6a358e))

<a name="0.9.0"></a>
# [0.9.0](https://github.com/wherebyus/platformservice/compare/0.9.0-rc.0...0.9.0) (2020-04-08)

<a name="0.9.0-rc.0"></a>
# [0.9.0-rc.0](https://github.com/wherebyus/platformservice/compare/0.8.0...0.9.0-rc.0) (2020-04-08)


### Features

* 🎸 adds a default brand feature ([158e4c8](https://github.com/wherebyus/platformservice/commit/158e4c8))
* adds API to get channel by ad types ([dca1c9b](https://github.com/wherebyus/platformservice/commit/dca1c9b))



<a name="0.8.0"></a>
# [0.8.0](https://github.com/wherebyus/platformservice/compare/0.7.0...0.8.0) (2020-04-06)



<a name="0.7.0"></a>
# [0.7.0](https://github.com/wherebyus/platformservice/compare/0.6.0...0.7.0) (2020-04-06)


### Features

* 🎸 Adds a bunch of brand creation endpoints and refactors ([8942e35](https://github.com/wherebyus/platformservice/commit/8942e35))
* 🎸 Adds API endpoint to get a users ad credits ([6aeecab](https://github.com/wherebyus/platformservice/commit/6aeecab))



<a name="0.6.0"></a>
# [0.6.0](https://github.com/wherebyus/platformservice/compare/0.5.1...0.6.0) (2020-04-01)


### Features

* 🎸 a callback URL with query params ([#15](https://github.com/wherebyus/platformservice/issues/15)) ([8538ea1](https://github.com/wherebyus/platformservice/commit/8538ea1))
* 🎸 add revenue share to brand condiguration ([#14](https://github.com/wherebyus/platformservice/issues/14)) ([fbab84b](https://github.com/wherebyus/platformservice/commit/fbab84b)), closes [#2069](https://github.com/wherebyus/platformservice/issues/2069)



<a name="0.5.1"></a>
## [0.5.1](https://github.com/wherebyus/platformservice/compare/0.5.0...0.5.1) (2020-03-30)


### Bug Fixes

* 🐛 Removes stray "middleware" class that doesnt exist ([fb46a3f](https://github.com/wherebyus/platformservice/commit/fb46a3f))



<a name="0.5.0"></a>
# [0.5.0](https://github.com/wherebyus/platformservice/compare/0.4.0...0.5.0) (2020-03-27)


### Features

* 🎸 Adds API endpoint to get user orders ([0c1bdc4](https://github.com/wherebyus/platformservice/commit/0c1bdc4))
* adds new fruitcake cors ([1350cae](https://github.com/wherebyus/platformservice/commit/1350cae))
* adds userId to order creation API ([8515a3e](https://github.com/wherebyus/platformservice/commit/8515a3e))
* cors headers are now sent for every route ([7b39496](https://github.com/wherebyus/platformservice/commit/7b39496))



<a name="0.4.0"></a>
# [0.4.0](https://github.com/wherebyus/platformservice/compare/0.3.0...0.4.0) (2020-03-20)


### Bug Fixes

* corrects issue passing parameter from middleware ([13421cb](https://github.com/wherebyus/platformservice/commit/13421cb))


### Features

* 🎸 Adds endpoints to charge and place an order via Platfrm ([#8](https://github.com/wherebyus/platformservice/issues/8)) ([48cbeaa](https://github.com/wherebyus/platformservice/commit/48cbeaa))
* 🎸 Adds non-passported channel API, tests, suite ([#9](https://github.com/wherebyus/platformservice/issues/9)) ([3aaf129](https://github.com/wherebyus/platformservice/commit/3aaf129))



<a name="0.3.0"></a>
# [0.3.0](https://github.com/wherebyus/platformservice/compare/0.2.1...0.3.0) (2020-03-13)



<a name="0.2.1"></a>
## [0.2.1](https://github.com/wherebyus/platformservice/compare/0.2.0...0.2.1) (2020-03-11)


### Bug Fixes

* 🐛 Adds the channelSlug to the brand creation suite ([#6](https://github.com/wherebyus/platformservice/issues/6)) ([caf9915](https://github.com/wherebyus/platformservice/commit/caf9915))



<a name="0.2.0"></a>
# [0.2.0](https://github.com/wherebyus/platformservice/compare/7a720a0...0.2.0) (2020-03-06)


### Features

* 🎸 adds brands, features, and configuration schemas ([e2914a4](https://github.com/wherebyus/platformservice/commit/e2914a4))
* 🎸 adds the CR part of CRUD for Brand API stuff ([b06c68a](https://github.com/wherebyus/platformservice/commit/b06c68a))
* 🎸 Adds Update and Delete for brands and configs ([02d790a](https://github.com/wherebyus/platformservice/commit/02d790a))
* 🎸 Bootstraps this PlatformService ([7a720a0](https://github.com/wherebyus/platformservice/commit/7a720a0))

