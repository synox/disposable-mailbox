/**
 * An Angular directive for stickyfill (position sticky polyfill)
 *
 * @version v0.1.0 - 2016-08-31
 * @author Corey Wilson <corey@eastcodes.com>
 * @license Unlicense, http://unlicense.org/
 */
!function(e,i){"use strict";if("function"==typeof define&&define.amd)define(["angular","stickyfill"],i);else{if("undefined"==typeof module||"object"!=typeof module.exports)return i(e.angular,e.Stickyfill);module.exports=i(require("angular"),require("stickyfill"))}}(window,function(e,i){"use strict";function t(){function e(e,t,n){if("object"!=typeof i)throw new Error("stickyfill.js not loaded");i.add(t[0]),e.$on("$destroy",function(){i.remove(t[0])})}var t={link:e,restrict:"A"};return t}if("function"==typeof i)var i=i();var n="ec.stickyfill";return e.module(n,[]).directive("ecStickyfill",t),n});
