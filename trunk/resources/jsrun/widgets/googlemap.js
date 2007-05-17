if(!PHP2Go.included[PHP2Go.baseUrl+'widgets/googlemap.js']){function GoogleMap(attrs,func){this.Widget(attrs,func);this.container=null;this.center=null;this.bounds=null;this.locations=[];this.map=null;}GoogleMap.extend(Widget,'Widget');GoogleMap.prototype.setup=function(){this.container=$(this.attributes['id']);this.center=new GLatLng(this.attributes['center'].lat,this.attributes['center'].lng);this.bounds=new GLatLngBounds();for(var i=0;i<this.attributes['locations'].length;i++){var point=new GLatLng(this.attributes['locations'][i].lat,this.attributes['locations'][i].lng);this.bounds.extend(point);this.locations.push(point);}Event.addListener(window,'unload',GUnload);var loc=this.locations;var info=$(this.attributes['id']+'_locations').getElementsByTagName('div');if(GBrowserIsCompatible()){var map=this.map=new GMap2(this.container);(!this.attributes['draggable'])&&(map.disableDragging());map.addControl(new GMapTypeControl());map.addControl(new GLargeMapControl());map.setCenter(this.center);loc.walk(function(item,idx){var marker=new GMarker(item);if(info[idx].innerHTML!=''){GEvent.addListener(marker,'click',function(){marker.openInfoWindowHtml(info[idx].innerHTML);});}map.addOverlay(marker);});map.setZoom(this.attributes['zoom']||map.getBoundsZoomLevel(this.bounds));}};PHP2Go.included[PHP2Go.baseUrl+'widgets/googlemap.js']=true;}