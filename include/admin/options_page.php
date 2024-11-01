<style>
.help_icon {
    position: relative;
    top: 8px;
    padding-left: 2px;
}
</style>
<div class="wrap">
<h2>MoneyPress : CafePress Edition</h2>
<h3>For help setting up and using this plugin, please visit the <a href="http://redmine.cybersprocket.com/projects/wpcafepress/wiki" target="cyber sprocket">MoneyPress : CafePress Edition Knowledgebase</a></h3>

  <form method="post" action="options.php">
    <?php settings_fields('qcp-settings'); ?>

    <div id="poststuff" class="metabox-holder">
      <div class="meta-box-sortables">
        <script type="text/javascript">
          jQuery(document).ready(function($) {
              $('.postbox').children('h3, .handlediv').click(function(){
                  $(this).siblings('.inside').toggle();
              });
          });
        </script>

        <div class="postbox">
          <div class="handlediv" title="Click to toggle"><br/></div>
          <h3 class="hndle">
            <span>License Settings</span>
          </h3>
          <div class="inside">
            <p>
              To obtain a key, please purchase this plugin from
              <a href="http://www.cybersprocket.com/products/wpquickcafepress/" target="_new">http://www.cybersprocket.com/products/wpquickcafepress/</a>
            </p>

            <table class="form-table" style="margin-top: 0pt;">
              <tr valign="top">
                <th scope="row">License Key *</th>
                <td>
                  <input type="text" <?= (!get_option('qcp-purchased')) ? 'name="qcp-license_key"' : '' ?> value="<?= get_option('qcp-license_key'); ?>" <?= (get_option('qcp-purchased')) ? 'disabled' : ''?> />
                  <? if (get_option('qcp-purchased')) { ?>
                  <input type="hidden" name="qcp-license_key" value="<?= get_option('qcp-license_key')?>"/>
                  <? } ?>
                  <?= (get_option('qcp-license_key') == '') ? '<span><font color="red">Without a license key, this plugin will only function for Admins</font></span>' : '' ?>
                  <?= ( !(get_option('qcp-license_key') == '') && !get_option('qcp-purchased') ) ? '<span><font color="red">Your license key could not be verified</font></span>' : '' ?>

                  <? if (!get_option('qcp-purchased')) { ?>
                  <div>
                    <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=NRMZK9MRR7AML" target="_new">
                      <img alt="PayPal - The safer, easier way to pay online!" src="https://www.paypal.com/en_US/i/btn/btn_buynowCC_LG.gif" />
                    </a>
                  </div>
                  <div>
                    <p>Your license key is emailed to your PayPal email address. It will arrive within 5 minutes of your purchase.</p>
                  </div>
                  <? } else { ?>
                  <div>
                      <span><font color="green">Valid license key.  Thanks for your purchase!</font></span>
                  </div>                      
                  <? } ?>
                </td>
              </tr>
            </table>
          </div>
        </div>


        <div class="postbox">
          <div class="handlediv" title="Click to toggle"><br/></div>
          <h3 class="hndle">
            <span>Store Configuration</span>
          </h3>
          <div class="inside">
            <p>Complete the settings below to configure your Quick store.</p>

            <table class="form-table" style="margin-top: 0pt;">
              <tr valign="top">
                <th scope="row">CafePress API Key *</th>
                <td>
                  <input type="text" size="30" name="config_cpapikey" value="<?= get_option('config_cpapikey'); ?>" />
                  <a href="http://redmine.cybersprocket.com/projects/wpcafepress/wiki/Getting_A_Cafepress_Developer_API_Key" target="cyber sprocket labs"><img src="<?= QCPPLUGINURL ?>/images/question_orange_24px.png" class="help_icon"></a>                  
                  <div>
                    <p>                    
                    Until you acquire your own api key, you can use our demo key &quot;ut3dcs8rr3svqt5r4r2u8677&quot; (without the quotes). 
                    This is a shared demo key and should not be used to run your plugin.
                    </p>
                  </div>
                </td>
              </tr>

              <tr valign="top">
                <th scope="row">Commission Junction Affiliate ID (CJ PID)</th>
                <td>
                  <input type="text" size="30" name="config_cjpid" value="<?= get_option('config_cjpid'); ?>" />
                  <a href="http://redmine.cybersprocket.com/projects/wpcafepress/wiki/Getting_A_Commission_Junction_PID" target="cyber sprocket labs"><img src="<?= QCPPLUGINURL ?>/images/question_orange_24px.png"  class="help_icon"></a><br />
                  <div><p>
                  This setting is optional.                    
                  <br/>
                  If you are a CafePress affiliate, enter your CJ PID here and we'll build your referal links automatically 
                  so you can earn commission on verified sales from your CafePress sales.  
                  Until you acquire your own CJ PID you can use our demo PID 3783719.  
                  This is a shared demo key and should not be used to run your plugin. 
                  You will NOT earn commissions if you leave this PID key in place.
                  </p></div>
                </td>
              </tr>
              
            </table>
          </div>
        </div>

        <div class="postbox">
          <div class="handlediv" title="Click to toggle"><br/></div>
          <h3 class="hndle">
            <span>Product Display Settings</span>
          </h3>
          <div class="inside">
              <p>These settings can be changed per individual post</p>

              <table class="form-table" style="margin-top: 0pt;">
                <tr valign="top">
                  <th scope="row">Default # of products to preview on the main page/archive pages:</th>
                  <td>
                    <input name="display_numtopreview" type="text" value="<?= get_option('display_numtopreview'); ?>" size="4">
                    <span>(Leave blank to show all products)</span>
                  </td>
                </tr>
                  <tr valign="top">
                    <th scope="row">Limit the number of products on single post pages to</th>
                    <td>
                      <input name="display_numtoshow" type="text" value="<?= get_option('display_numtoshow'); ?>" size="4">
                      <span>(Leave blank to show all products)</span>
                    </td>
                  </tr>
                </table>
              </div>
            </div>

            <div class="postbox">
              <div class="handlediv" title="Click to toggle"><br/></div>
              <h3 class="hndle">
                <span>Style Settings</span>
              </h3>
              <div class="inside">
                <p>Edit the styles for the store grid. Copy and paste the sample styles the first time you set up the plugin, then make changes as necessary to match your site's style.</p>

                <table class="form-table" style="margin-top: 0pt">
                  <tr valign="top">
                    <th scope="row">Style for entire container</th>
                    <td>
                      .cpstore_css_container<br />{<br /><textarea cols="40" rows="2" name="css_container"><?= get_option('css_container'); ?></textarea><br />}
                      <div>
                        Sample:<br/>
                        <i>margin-left:0px;</i>
                      </div>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row">Style for category header</th>
                    <td>
                      .cpstore_css_category<br />{<br /><textarea cols="40" rows="2" name="css_category"><?= get_option('css_category'); ?></textarea><br />}
                      <div>
                        Sample:<br/>
                          <i>
                          font-size:125%;<br/>
                          font-weight:bold;
                          </i>
                      </div>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row">Style for category menu</th>
                    <td>
                      .cpstore_css_catmenu<br />{<br /><textarea cols="40" rows="2" name="css_catmenu"><?= get_option('css_catmenu'); ?></textarea><br />}
                      <div>
                        Sample:<br/>
                        <i>text-align:center;</i>
                      </div>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row">Style for each cell</th>
                    <td>
                      .cpstore_css_float<br />{<br /><textarea cols="40" rows="7" name="css_float"><?= get_option('css_float'); ?></textarea><br />}
                      <div>
                        Sample:<br/>
                        <i>
                          float: left;<br/>
                          width: 158px;<br/>
                          height: 250px;<br/>
                          padding: 2px;<br/>
                          background:#F5F5F5;<br/>
                          border: #999999 1px solid;<br/>
                          text-align: center;<br/>
                          margin-right: 4px;<br/>
                          margin-bottom: 6px;<br/>
                        </i>
                      </div>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row">Style for the thumbnail image</th>
                    <td>
                      .cpstore_css_float img<br />{<br /><textarea cols="40" rows="7" name="css_float_img"><?= get_option('css_float_img'); ?></textarea><br />}
                      <div>
                        Sample:<br/>
                        <i>
                          padding: 2px;<br/>
                          background:#999999;<br/>
                          margin-top:2px;<br/>
                          border: 0px;<br/>
                          margin-bottom: 0;<br/>
                        </i>
                      </div>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row">Style for the text</th>
                    <td>
                      .cpstore_css_float p<br />{<br /><textarea cols="40" rows="7" name="css_float_p"><?= get_option('css_float_p'); ?></textarea><br />}
                      <div>
                        Sample:<br/>
                        <i>
                          margin: 0;<br/>
                          text-align: center;<br/>
                          font-weight:bold;<br/>
                          line-height:normal;<br/>
                        </i>
                      </div>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row">Style for the buy now/price link</th>
                    <td>
                      .cpstore_css_price a<br />{<br /><textarea cols="40" rows="7" name="css_price_a"><?= get_option('css_price_a'); ?></textarea><br />}
                      <div>
                        Sample:<br/>
                        <i>
                          font-size:100%;<br/>
                          font-weight:bold;<br/>
                          text-decoration:none;<br/>
                          color:#000;<br/>
                          font-weight:bold;<br/>
                          border:2px solid;<br/>
                          padding:1px 1px 3px 1px;<br/>
                          border-color: #eee #999 #666 #e3e3e3;<br/>
                          background:#fff;<br/>
                        </i>
                      </div>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row">Style for each cell</th>
                    <td>
                      .cpstore_css_float_hover<br />{<br /><textarea cols="40" rows="7" name="css_float_hover"><?= get_option('css_float_hover'); ?></textarea><br />}
                      <div>
                        Sample:<br/>
                        <i>
                          float: left;<br/>
                          width: 158px;<br/>
                          height: 250px;<br/>
                          padding: 2px;<br/>
                          background:#E8E8E8;<br/>
                          border: #9F9F9F 1px solid;<br/>
                          text-align: center;<br/>
                          margin-right: 4px;<br/>
                          margin-bottom: 6px;<br/>
                        </i>
                      </div>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row">Style for the thumbnail image (hover)</th>
                    <td>
                      .cpstore_css_float_hover img<br />{<br /><textarea cols="40" rows="7" name="css_float_hover_img"><?= get_option('css_float_hover_img'); ?></textarea><br />}
                      <div>
                        Sample:<br/>
                        <i>
                          padding: 2px;<br/>
                          background:#999999;<br/>
                          margin-top:2px;<br/>
                          border: 0px;<br/>
                          margin-bottom: 0;<br/>
                        </i>
                      </div>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row">Style for the text (hover)</th>
                    <td>
                      .cpstore_css_float_hover p<br />{<br /><textarea cols="40" rows="7" name="css_float_hover_p"><?= get_option('css_float_hover_p'); ?></textarea><br />}
                      <div>
                        Sample:<br/>
                        <i>
                          margin: 0;<br/>
                          text-align: center;<br/>
                          font-weight:bold;<br/>
                          line-height:normal;<br/>
                        </i>
                      </div>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row">Style for the price link (hover)</th>
                    <td>
                      .cpstore_css_price a:hover<br />{<br /><textarea cols="40" rows="7" name="css_price_hover_a"><?= get_option('css_price_hover_a'); ?></textarea><br />}
                      <div>
                        Sample:<br/>
                        <i>border-color: #666 #e3e3e3 #eee #999;</i>
                      </div>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row">Style for the &quot;View All&quot; link</th>
                    <td>
                      .cpstore_css_viewall a<br />{<br /><textarea cols="40" rows="7" name="css_viewall"><?= get_option('css_viewall'); ?></textarea><br />}
                      <div>
                        Sample:<br/>
                        <i>
                          text-align:center;<br/>
                          font-size:125%;<br/>
                        </i>
                      </div>
                    </td>
                  </tr>
                  </table>
                </div>
              </div>

              <div class="postbox">
                <div class="handlediv" title="Click to toggle"><br/></div>
                <h3 class="hndle">
                  <span>Product Info</span>
                </h3>
                <div class="inside" style="min-height: 150px;">
                  <img src="<?= QCPPLUGINURL ?>/images/CSL_Logo_Only.jpg" style="float: left; padding: 5px;"/>
                  <h4>This plugin was written and created by Cyber Sprocket Labs</h4>
                  <p>
                    Cyber Sprocket Labs provides technical consulting
                    services for small-to-medium sized businesses.  If you’ve got an
                    online business concept, a new piece of cool software you want
                    written, or just need some help getting your technical team organized
                    and pointed in the right direction, we can help.
                  </p>
                  <p>
                    For more information, please visit our website at <a href="http://www.cybersprocket.com" target="_new">www.cybersprocket.com</a>
                  </p>
                  <p>
                  Visit the <a href="http://www.cybersprocket.com/products/wpquickcafepress/" target="_new">MoneyPress : CafePress Edition page</a> to learn more.
                  </p>
                </div>
              </div>


            </div>
          </div>


          <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
          </p>


        </form>
      </div>
