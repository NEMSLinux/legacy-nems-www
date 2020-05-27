/*
 * ----------------------------- ActionSlide -------------------------------------
 * Simple animation library for action movie credits in JavaScript, supporting
 * all major browsers - IE9+, Firefox, Safari, Chrome and Opera
 * Prerequisite: JQuery
 *
 * Copyright (c) 2013 - 2014 Liron Aichel, work.liron@gmail.com
 *
 * Licensed under MIT-style license:
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 */
 
 var ActionSlide = (function ($) {
	'use strict';

	function ActionCredits(screen, params) {
        this.screen = screen;
        this.slides = [];
		this.params = params;
		this.isPlaying = false;

        this.addSlide = function(slide) {
            if (slide instanceof ActionSlide) {
                this.slides.push(slide);
            }
        }
		
		this.preloadImageSlides = function() {
			for (var slide in this.slides){
				if (typeof(this.slides[slide].imageURL) !== 'undefined') {
					$('<img/>')[0].src = this.slides[slide].imageURL;
				}
			}
		}

        this.events = {
            play: "playing",
            end: "ended",
            slideIn: "slideIn",
            slideOut: "slideOut"
        }
    }

    ActionCredits.prototype.play = function() {
        var that = this;

        $(that).trigger(that.events.play);

		that.isPlaying = true;
		
		if (that.params.animateBackground) {
			var showRandomLines = function() {
				var lineLength = Math.floor((Math.random()*(that.screen.width() / 2))+(that.screen.width() / 5));
				var delayDuration = Math.floor((Math.random()*1000)+500);
				var top = Math.floor((Math.random()*that.screen.height())+1);
				var direction = Math.floor((Math.random()*2)+1) == 1? 'ltr': 'rtl';
				var animType;
			
				var line = $("<div class='actionslide-bglines' style='position: absolute; top: " + top + "px; width: " + lineLength + "px;'></div>");
				that.screen.prepend(line);
				if (direction == 'ltr') {
					line.css('left','-' + lineLength + 'px');
					animType = {
						left: that.screen.width() + 50
					};
				} else {
					line.css('right','-' + lineLength + 'px');
					animType = {
						right: that.screen.width() + 50
					};
				}
				
				line.animate(animType).promise().done(function() {
					line.remove();
				});
				
				if (that.isPlaying) {
					setTimeout(showRandomLines, delayDuration);
				}
			}
			showRandomLines();
		}
		
		that.preloadImageSlides();
		
        var allDef = $.Deferred();

        var playBySlide = function(index){
            
            var slide = that.slides[index];
            var slideHTML = slide.render();
            var animDef = $.Deferred();

            if (slide.params.align) {
                slideHTML.css('text-align',slide.params.align);
            }

			slideHTML.css('opacity','0');
            that.screen.append(slideHTML);
			
			var middlePoint = (that.screen.width() / 2) - (slideHTML.width() / 2);
			slideHTML.css('top',(that.screen.height() / 2 - slideHTML.height() / 2) + 'px');
			
            if (slide.params.animation == 'fade') {
                slideHTML.css('left',middlePoint+'px');
            }
			
			if (slide.params.direction == 'rtl') {
                slideHTML.css('right','0px');
            }

            if (!slide.params.easing) {
                slide.params.easing = 'linear';
            }
            
            var endAnimation = function(){
                var animationType;
                switch (slide.params.animation) {
                    case 'fade':
                        animationType = {
                            opacity: 0
                        };
                        break;

                    default:
						if (slide.params.direction == 'rtl') {
							animationType = {
								right: that.screen.width(),
								opacity: 0
							};
						} else {
							animationType = {
								left: that.screen.width(),
								opacity: 0
							};
						}
                        break;
                }

                var endAnim = slideHTML.animate(animationType,{
                    duration: 700,
                    easing: slide.params.easing
                }).promise();

                endAnim.done(function() {
                    $(that).trigger(that.events.slideOut,[index]);
                    slideHTML.remove();
                    animDef.resolve();
                });
            }

            var midAnimation = function(){
                var animationType;
                switch (slide.params.animation) {
                    case 'fade':
                        animationType = {
                            opacity: 1
                        };
                        break;

                    default:
						if (slide.params.direction == 'rtl') {
							animationType = {
								right: middlePoint - slideHTML.width()
							};
						} else {
							animationType = {
								left: middlePoint - slideHTML.width()
							};
						}
                        
                        break;
                }

                var anim = slideHTML.animate(animationType,{
                    duration: slide.pauseDuration,
                    easing: slide.params.easing
                }).promise();

                anim.done(endAnimation);
            }

            var animationType;
            switch (slide.params.animation) {
                case 'fade':
                    animationType = {
                        opacity: 1
                    };
                    break;

                default:
					if (slide.params.direction == 'rtl') {
						animationType = {
							right: middlePoint,
							opacity: 1
						};
					} else {
						animationType = {
							left: middlePoint,
							opacity: 1
						};
					}
                    break;
            }
			
            $(that).trigger(that.events.slideIn,[index]);

            var startAnim = slideHTML.animate(animationType,{
                    duration: 700,
                    easing: slide.params.easing
                }).promise();

            startAnim.done(midAnimation);

            animDef.done(function() {
                if (index < that.slides.length - 1){
                    index++;
                    playBySlide(index);
                } else {
                    allDef.resolve();
					that.isPlaying = false;
                    $(that).trigger(that.events.end);
                }
            });
        };

        playBySlide(0);

        return allDef;
    }

    function ActionSlide (title, html, pauseDuration, params) {
        this.title = title;
        this.html = html;
        this.pauseDuration = pauseDuration;
        this.params = params;

        this.template = 
			'<div class="actionslide-container"> \
                <div class="actionslide-title"> \
                </div> \
                <div class="actionslide-content"> \
                </div> \
            </div>';
    }

    ActionSlide.prototype.render = function() {
		var slideContent = $(this.template);
		
		slideContent.find(".actionslide-title").html(this.title);
		
		slideContent.find(".actionslide-content").html(this.html);

        return slideContent;
    }
	
    $.fn.actionSlide = function(options) {
    
        if (this.length > 1) {
            var returnValues = [];
            this.each(function() {
                returnValues.push($(this).actionSlide(options));
            });
            return returnValues;
        }
        
        // if menu already exists
        if (this.data('actionSlide')) {
            return this.data('actionSlide');
        }
    
        // verify element is valid
        if (this && 
            this.is('ul')) {
            
            var htmlOptions = {
                animationBG: this.attr('data-animate-background')
            };

            var credits = new ActionCredits($(this).parent(),{
                animateBackground: htmlOptions.animationBG ? htmlOptions.animationBG==="true":true
            });

            $(this).hide();

            $(this).find('>li').each(function(i,v) {
                var elem = $(v);

                var htmlOptions = {
                    title: elem.attr('data-title'),
                    duration: elem.attr('data-duration'),
                    animation: elem.attr('data-animation'),
                    direction: elem.attr('data-direction'),
                    textAlign: elem.attr('data-align'),
                    easing: elem.attr('data-easing')
                };

                var options = $.extend({
                    title: '',
                    duration: 2000,
                    animation: 'slide',
                    direction: 'ltr',
                    textAlign: 'left',
                    easing: 'linear'
                },htmlOptions,options);

                credits.addSlide(new ActionSlide(
                    options.title,
                    elem.html(),
                    parseInt(options.duration,10),
                    {
                        animation: options.animation,
                        align: options.textAlign,
                        direction: options.direction
                    }
                ));
            });
            
            this.data('actionSlide',credits);
            
            setTimeout(function() {credits.play();},200);

            return credits;
        }
    }
	
    $(document).ready(function() {
        $("body [data-role='actionSlide']").actionSlide();
    });

	return {
		Credits : ActionCredits,
		Slide: ActionSlide,
        version: "1.0.5"
	};
}($));