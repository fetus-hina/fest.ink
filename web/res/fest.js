!function(t,e){"use strict";var n=t.jQuery,r=Number.NaN;n(t.document).ready(function(){var a=n(".container[data-fest]").attr("data-fest");if((a+"").match(/^\d+$/)){var i=n(".total-rate"),u=n(".sample-count"),o=n(".total-progressbar"),s=n(".rate-graph"),c=n(".last-updated-at"),f=n(".last-fetched-at");if(!(i.length<1&&u.length<1&&o.length<1&&s.length<1&&c.length<1&&f.length<1)){var l=function(){var t=function(t){return t.toString().replace(/([\d]+?)(?=(?:\d{3})+$)/g,function(t){return t+","})},l=function(t){for(var e=0,n=0,a=0;a<t.wins.length;++a)e+=t.wins[a].r,n+=t.wins[a].g;var i=e+n;return{r:i>0?e/i:r,g:i>0?n/i:r,rSum:i>0?e:r,gSum:i>0?n:r}},g=function(t){i.each(function(){var a=n(this),i=function(){switch(a.attr("data-team")){case"red":return t.r;case"green":return t.g;default:return r}}();a.text(i===e||isNaN(i)?"???":Math.round(1e3*i)/10+"%")})},h=function(e){console.log(e),u.text(isNaN(e.rSum)||isNaN(e.gSum)?"???":t(e.rSum+e.gSum))},m=function(t){o.each(function(){var a=n(this),i=function(){switch(a.attr("data-team")){case"red":return t.r;case"green":return t.g;default:return r}}();a.width(i===e||isNaN(i)?"0%":100*i+"%")})},d=function(t){return{series:{stack:!0,lines:{show:!0,fill:!0,steps:!1}},xaxis:{mode:"time",minTickSize:[30,"minute"],timeformat:"%H:%M",twelveHourClock:!1,timezone:"browser",min:1e3*t.begin,max:1e3*t.end},yaxis:{min:0,max:100},colors:["#d9435f","#5cb85c"]}},v=function(t){var e=s.filter(".rate-graph-short");if(e.length>0){for(var r=[],a=[],i=0;i<t.wins.length;++i){var u=t.wins[i];u.r+u.g>0&&(r.push([1e3*u.at,100*u.r/(u.r+u.g)]),a.push([1e3*u.at,100*u.g/(u.r+u.g)]))}e.each(function(){var e=n(this);e.empty(),n.plot(e,[r,a],d(t.term))})}},p=function(t){var e=s.filter(".rate-graph-whole");if(e.length>0){var r=t.wins.slice(0);r.sort(function(t,e){return t.at-e.at});for(var a=0,i=0,u=[],o=[],c=0;c<r.length;++c){var f=r[c];a+=f.r,i+=f.g,a+i>0&&(u.push([1e3*f.at,100*a/(a+i)]),o.push([1e3*f.at,100*i/(a+i)]))}e.each(function(){var e=n(this);e.empty(),n.plot(e,[u,o],d(t.term))})}},w=function(t,n){var r=function(t){var e=function(t){return t=~~t,(t>9?"":"0")+t};return t.getFullYear()+"-"+e(t.getMonth()+1)+"-"+e(t.getDate())+" "+e(t.getHours())+":"+e(t.getMinutes())},a=Math.max.apply(null,n.wins.map(function(t){return t.at}));c.text(1>a||a===e||isNaN(a)?"???":r(new Date(1e3*a))),f.text(r(t))},N=new Date;n.getJSON("/"+encodeURIComponent(a)+".json",{_t:Math.floor(N/1e3)},function(t){var e=l(t);g(e),m(e),h(e),v(t),p(t),w(N,t)})};t.setTimeout(function(){l.call(t),t.setInterval(function(){l.call(t)},6e5)},1)}}})}(window);