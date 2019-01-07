<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG;

class Preloader
{
    static public function preloaderJsHtml()
    {
        static $include_script;

        $html = '';

        if (!isset($include_script)) {

            $include_script = false;

            ob_start(); ?>
            <script>
                var OLOG = OLOG || {};
                OLOG.preloader = OLOG.preloader || {
                    template: function() {
                        return '<div id="preloader" style="z-index: 100000;position: fixed;top: 0;bottom: 0;left: 0;right: 0;background-color: rgba(255, 255, 255, 0.6);">\
								<svg style="position: absolute;top: 0;bottom: 0;left: 0;right: 0;display: block;width: 100px;height: 100px;margin: auto;" width="100px" height="100px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="uil-ring-alt">\
									<rect x="0" y="0" width="100" height="100" fill="none" class="bk"></rect>\
									<circle cx="50" cy="50" r="40" stroke="rgba(0,0,0,0.5)" fill="none" stroke-width="10" stroke-linecap="round"></circle>\
									<circle cx="50" cy="50" r="40" stroke="#ffffff" fill="none" stroke-width="6" stroke-linecap="round">\
										<animate attributeName="stroke-dashoffset" dur="2s" repeatCount="indefinite" from="0" to="502"></animate>\
										<animate attributeName="stroke-dasharray" dur="2s" repeatCount="indefinite" values="150.6 100.4;1 250;150.6 100.4"></animate>\
									</circle>\
								</svg>\
							</div>';
                    },

                    show: function ($container_obj) {
                        if (this.$preloader) {
                            this.hide();
                        }
                        this.$preloader = $(this.template());

                        if (!$container_obj) {
                            $('body').prepend(this.$preloader.css('position', 'fixed'));
                        } else {
                            this.$container = $container_obj;
                            this.container_style = '';
                            if (this.$container.css('position') == 'static') {
                                if (this.$container.attr('style')) {
                                    this.container_style = this.$container.attr('style');
                                }
                                this.$container.css('position', 'relative');
                            }
                            $container_obj.prepend(this.$preloader.css('position', 'absolute'));
                        }
                    },

                    hide: function () {
                        if (this.$container) {
                            if (this.container_style) {
                                this.$container.attr('style', this.container_style);
                            } else {
                                this.$container.removeAttr('style');
                            }
                        }
                        this.$preloader.remove();
                    }
                };
            </script>
            <?php
            $html = ob_get_clean();

        }
        return $html;
    }
}
