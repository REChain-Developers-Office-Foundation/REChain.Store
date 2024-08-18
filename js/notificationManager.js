function notificationManager(options={})
{this.constructor=function(options)
{if(options.container)
{this.setContainer(options.container)}
if(options.position)
{if(this.toString(options.position))
{this.setPosition(options.position);}}};this.color=null;this.setColor=function(color)
{if(this.isString(color))
{this.color=color;}}
this.position=null;this.setPosition=function(npos)
{if(this.isString(npos))
{this.findContainer();switch(npos)
{case "topleft":this.position=npos;this.container.removeClass();this.container.addClass('topleft');return true;break;case "topright":this.position=npos;this.container.removeClass();this.container.addClass('topright');return true;break;case "bottomleft":this.position=npos;this.container.removeClass();this.container.addClass('bottomleft');return true;break;case "bottomright":this.position=npos;this.container.removeClass();this.container.addClass('bottomright');return true;break;default:return false;break;}}
return false;};this.container=false;this.setContainer=function(container)
{if(container instanceof jQuery)
{this.container=container;}
else if(this.isString(container))
{if($(container).length)
{this.container=$(container);}}};this.addNotification=function(options={})
{this.findContainer();if(options.message)
{if(!this.isString(this.position))
{this.position="bottomright";this.container.addClass('bottomright');}
if(options.color&&this.isString(options.color))
{this.setColor(options.color);}
var node=$('<div/>').addClass('notification')
if(this.isString(this.color))
{node.css('color',this.color);}
var pad=$('<div/>').addClass('pad')
var msg=$('<div/>').addClass('message').html(options.message)
var close=$('<div/>').addClass('close').css('cursor','pointer')
var closeBtn=$('<div/>').addClass('close-btn')
close.off('click').on('click',function(e)
{var n=$(this).parent().parent();n.animate({left:'-=50px',opacity:"0"},"fast",function(){n.remove();});});var pC=$('<div/>').addClass('progressContainer')
var p=$('<div/>').addClass('progress')
if(options.animate===true)
{node.addClass("n-animate-in");}
if(options.autoRemove===true)
{if(options.animate===true)
{node.addClass("n-animate");p.addClass('progress-animate');node.bind('animationend webkitAnimationEnd oAnimationEnd MSAnimationEnd',function(e){$(this).remove();});}
else
{p.addClass('progress-animate');node.bind('animationend webkitAnimationEnd oAnimationEnd MSAnimationEnd',function(e){$(this).remove();});}}
if(this.isString(options.backgroundColor))
{node.css('background-color',options.backgroundColor);}
if(this.isString(options.progressColor))
{p.css('background-color',options.progressColor);}
pad.append(msg);pad.append(close.append(closeBtn));pC.append(p);node.append(pad);node.append(pC);this.container.append(node);return true;}};this.findContainer=function()
{if(!this.container)
{if($('#notificationsContainer').length)
{this.container=$('#notificationsContainer');}
else
{this.container=$('body');}}}
this.isString=function(s)
{return Object.prototype.toString.call(s)==="[object String]";};this.constructor(options);}