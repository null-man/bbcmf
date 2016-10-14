/*
 Data plugin for Highcharts

 (c) 2012-2013 Torstein Hønsi
 Last revision 2013-06-07

 License: www.highcharts.com/license
*/

!function(t){var e=t.each,s=function(t,e){this.init(t,e)};t.extend(s.prototype,{init:function(t,e){this.options=t,this.chartOptions=e,this.columns=t.columns||this.rowsToColumns(t.rows)||[],this.columns.length?this.dataFound():(this.parseCSV(),this.parseTable(),this.parseGoogleSpreadsheet())},getColumnDistribution:function(){var s=this.chartOptions,i=s&&s.chart&&s.chart.type,n=[];e(s&&s.series||[],function(e){n.push((t.seriesTypes[e.type||i||"line"].prototype.pointArrayMap||[0]).length)}),this.valueCount={global:(t.seriesTypes[i||"line"].prototype.pointArrayMap||[0]).length,individual:n}},dataFound:function(){this.parseTypes(),this.findHeaderRow(),this.parsed(),this.complete()},parseCSV:function(){var t=this,s=this.options,i=s.csv,n=this.columns,o=s.startRow||0,r=s.endRow||Number.MAX_VALUE,a=s.startColumn||0,l=s.endColumn||Number.MAX_VALUE,u=0;i&&(i=i.replace(/\r\n/g,"\n").replace(/\r/g,"\n").split(s.lineDelimiter||"\n"),e(i,function(i,h){var p=t.trim(i),c=0===p.indexOf("#");h>=o&&r>=h&&!c&&""!==p&&(p=i.split(s.itemDelimiter||","),e(p,function(t,e){e>=a&&l>=e&&(n[e-a]||(n[e-a]=[]),n[e-a][u]=t)}),u+=1)}),this.dataFound())},parseTable:function(){var t,s=this.options,i=s.table,n=this.columns,o=s.startRow||0,r=s.endRow||Number.MAX_VALUE,a=s.startColumn||0,l=s.endColumn||Number.MAX_VALUE;i&&("string"==typeof i&&(i=document.getElementById(i)),e(i.getElementsByTagName("tr"),function(s,i){t=0,i>=o&&r>=i&&e(s.childNodes,function(e){("TD"===e.tagName||"TH"===e.tagName)&&t>=a&&l>=t&&(n[t]||(n[t]=[]),n[t][i-o]=e.innerHTML,t+=1)})}),this.dataFound())},parseGoogleSpreadsheet:function(){var t,e,s=this,i=this.options,n=i.googleSpreadsheetKey,o=this.columns,r=i.startRow||0,a=i.endRow||Number.MAX_VALUE,l=i.startColumn||0,u=i.endColumn||Number.MAX_VALUE;n&&jQuery.getJSON("https://spreadsheets.google.com/feeds/cells/"+n+"/"+(i.googleSpreadsheetWorksheet||"od6")+"/public/values?alt=json-in-script&callback=?",function(i){var n,h,i=i.feed.entry,p=i.length,c=0,m=0;for(h=0;p>h;h++)n=i[h],c=Math.max(c,n.gs$cell.col),m=Math.max(m,n.gs$cell.row);for(h=0;c>h;h++)h>=l&&u>=h&&(o[h-l]=[],o[h-l].length=Math.min(m,a-r));for(h=0;p>h;h++)n=i[h],t=n.gs$cell.row-1,e=n.gs$cell.col-1,e>=l&&u>=e&&t>=r&&a>=t&&(o[e-l][t-r]=n.content.$t);s.dataFound()})},findHeaderRow:function(){e(this.columns,function(){}),this.headerRow=0},trim:function(t){return"string"==typeof t?t.replace(/^\s+|\s+$/g,""):t},parseTypes:function(){for(var t,e,s,i,n=this.columns,o=n.length;o--;)for(t=n[o].length;t--;)e=n[o][t],s=parseFloat(e),i=this.trim(e),i==s?(n[o][t]=s,s>31536e6?n[o].isDatetime=!0:n[o].isNumeric=!0):(e=this.parseDate(e),0!==o||"number"!=typeof e||isNaN(e)?n[o][t]=""===i?null:i:(n[o][t]=e,n[o].isDatetime=!0))},dateFormats:{"YYYY-mm-dd":{regex:"^([0-9]{4})-([0-9]{2})-([0-9]{2})$",parser:function(t){return Date.UTC(+t[1],t[2]-1,+t[3])}}},parseDate:function(t){var e,s,i,n=this.options.parseDate;if(n&&(e=n(t)),"string"==typeof t)for(s in this.dateFormats)n=this.dateFormats[s],(i=t.match(n.regex))&&(e=n.parser(i));return e},rowsToColumns:function(t){var e,s,i,n,o;if(t)for(o=[],s=t.length,e=0;s>e;e++)for(n=t[e].length,i=0;n>i;i++)o[i]||(o[i]=[]),o[i][e]=t[e][i];return o},parsed:function(){this.options.parsed&&this.options.parsed.call(this,this.columns)},complete:function(){var e,s,i,n,o,r,a,l,u=this.columns,h=this.options;if(h.complete){for(this.getColumnDistribution(),u.length>1&&(e=u.shift(),0===this.headerRow&&e.shift(),e.isDatetime?s="datetime":e.isNumeric||(s="category")),r=0;r<u.length;r++)0===this.headerRow&&(u[r].name=u[r].shift());for(n=[],r=0,l=0;r<u.length;l++){for(i=t.pick(this.valueCount.individual[l],this.valueCount.global),o=[],a=0;a<u[r].length;a++)o[a]=[e[a],void 0!==u[r][a]?u[r][a]:null],i>1&&o[a].push(void 0!==u[r+1][a]?u[r+1][a]:null),i>2&&o[a].push(void 0!==u[r+2][a]?u[r+2][a]:null),i>3&&o[a].push(void 0!==u[r+3][a]?u[r+3][a]:null),i>4&&o[a].push(void 0!==u[r+4][a]?u[r+4][a]:null);n[l]={name:u[r].name,data:o},r+=i}h.complete({xAxis:{type:s},series:n})}}}),t.Data=s,t.data=function(t,e){return new s(t,e)},t.wrap(t.Chart.prototype,"init",function(s,i,n){var o=this;i&&i.data?t.data(t.extend(i.data,{complete:function(r){i.series&&e(i.series,function(e,s){i.series[s]=t.merge(e,r.series[s])}),i=t.merge(r,i),s.call(o,i,n)}}),i):s.call(o,i,n)})}(Highcharts);