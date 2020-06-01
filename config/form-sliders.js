var FormSliders=function(){return{initFormSliders:function(){$('#slider1').slider({min:0,max:500,slide:function(event,ui)
{$('#slider1-value').text(ui.value);}});$('#slider2').slider({min:0,max:500,range:true,values:[75,300],slide:function(event,ui)
{$('#slider2-value1').text(ui.values[0]);$('#slider2-value2').text(ui.values[1]);}});$('#slider3').slider({min:0,max:500,step:100,slide:function(event,ui)
{$('#slider3-value').text(ui.value);}});$('#slider1-rounded').slider({min:0,max:500,slide:function(event,ui)
{$('#slider1-value-rounded').text(ui.value);}});$('#slider2-rounded').slider({min:0,max:500,range:true,values:[75,300],slide:function(event,ui)
{$('#slider2-value1-rounded').text(ui.values[0]);$('#slider2-value2-rounded').text(ui.values[1]);}});$('#slider3-rounded').slider({min:0,max:500,step:100,slide:function(event,ui)
{$('#slider3-value-rounded').text(ui.value);}});}};}();