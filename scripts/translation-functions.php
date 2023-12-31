<?php

class TranslationFunctions {

    var $theme_dir;
    var $app_dir;

    public function __construct($vuewp_theme_dir, $vuewp_app_dir) {
        $this->theme_dir = $vuewp_theme_dir;
        $this->app_dir = $vuewp_app_dir;
    }
    
    public function translations_form() {
        $lang_info = $this->read_strings();
        $strings = $lang_info['strings'];
        $lang_codes = array_keys($strings);
        $languages = [];
        foreach ($lang_codes as $code) {
            $languages[$code] = $code;
            if (!empty($strings[$code]['language_name'])) {
                $languages[$code] = $strings[$code]['language_name'];
            }
        }
        ?>
        <div class="formline">
            <label for="langs"><?php _e('Select a language to edit', 'vuewp'); ?></label>
            <div class="input">
                <select name="langs" id="langs" class="half">
                    <?php foreach ($languages as $lcode => $lname) { ?>
                        <option value="<?php print $lcode; ?>"><?php print $lname; ?></option>
                    <?php } ?>
                </select>
                <button type="button" class="button" id="edit_button"><?php _e('Edit', 'vuewp'); ?></button>
            </div>
        </div>
    
        <div class="formline">
            <label for="new_code"><?php _e('Create a new language file', 'vuewp'); ?></label>
            <div class="input">
                <input type="text" name="new_code" id="new_code" placeholder="<?php _e('Language code', 'vuewp'); ?>">
                <input type="text" name="new_name" id="new_name" placeholder="<?php _e('Language name', 'vuewp'); ?>">
                <button type="button" class="button" id="create_button"><?php _e('Create', 'vuewp'); ?></button>
            </div>
        </div>
    
        <div class="translator" style="display: none;">
            <div class="strings"></div>
            <div class="editor">
                <div class="key-string">
                    <textarea id="key-string" readonly></textarea>
                </div>
                <div class="value-string">
                    <textarea id="value-string"></textarea>
                </div>
                <div class="buttons">
                    <!-- <input type="hidden" id="action" name="action"> -->
                    <input type="hidden" id="lang" name="lang">
                    <input type="hidden" id="strings" name="strings">
                    <button class="button" type="button" id="cancel_saving">
                        <?php _e('Cancel', 'vuewp'); ?>
                    </button>
                    <button class="button button-primary" type="button" id="save_language">
                        <?php _e('Save', 'vuewp'); ?>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="strings-source" style="display: none;">
            <?php foreach ($strings as $lng => $strs) { ?>
                <div class="lang-set" data-lang="<?php print $lng; ?>">
                    <h3><?php printf(__('Editing language file &apos;%s.json&apos; (%s)', 'vuewp'), $lng, $languages[$lng]); ?></h3>
                    <?php foreach ($strs as $key => $val) { ?>
                        <?php 
                        $new_item = in_array($key, $lang_info['new_items'][$lng]);
                        $vars = isset($lang_info['variables'][$key]) ? "<em> { " . join(', ', $lang_info['variables'][$key]) . " }</em>" : "";
                        ?>
                        <div class="str-line<?php if ($new_item) print ' not-saved' ?>">
                            <span class="key"><?php print "<span class='name'>{$key}</span>" . $vars; ?></span>
                            <span class="val"><?php print $val; ?></span>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
        <?php
    }
    
    public function create_file($lang) {
        $path = get_template_directory() . $this->app_dir . '/src/I18n/langs/' . $lang . '.json';
        if (!file_exists($path)) {
            $fh = fopen($path, "w");
            fclose($fh);
        }
    }
    
    public function save_strings($lang, $json) {
        $path = get_template_directory() . '/' . $this->app_dir . '/src/I18n/langs/' . $lang . '.json';
        $valid = json_decode(stripslashes($json), true);
        if (!is_array($valid)) {
            return false;
        }
        $str = json_encode($valid, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return !!file_put_contents($path, $str);
    }
    
    public function read_strings() {
        $app_dir = $this->theme_dir . '/' . $this->app_dir;
        $lang_files = listFiles($app_dir . '/src/I18n/langs');
        $components = listFiles($app_dir . '/src/components');
        $views = listFiles($app_dir . '/src/views');
        $files = array_merge($components, $views);

        global $code, $vars;
        $code = ["language_name" => ""];
        $vars = [];
        function proccess($matches) {
            global $code, $vars;
            foreach ($matches as $arr) {
                if (!empty($arr[1])) {
                    $str = $arr[1];
                    $code[$str] = "";
                    if (!empty($arr[2])) {
                        $vars[$str] = preg_split("/\s*,\s*/", trim(preg_replace("/, ?\{ ?([^\}]+) ?\}/", "$1", $arr[2])));
                    }
                }
            }
        }
        foreach ($files as $file) {
            $content = file_get_contents($file);
            $content = str_replace("\\'", "--apos--", $content);
            preg_match_all("/\bt[pl]? *\([\']([^\']+)[\']([^)]+)*\)/", $content, $matches, PREG_SET_ORDER);
            proccess($matches);
            $content = str_replace('\\"', "--quote--", $content);
            preg_match_all("/\bt[pl]? *\([\"]([^\"]+)[\"]([^)]+)*\)/", $content, $matches, PREG_SET_ORDER);
            proccess($matches);
        }
    
        $new_items = [];
        $all = [];
        foreach ($lang_files as $lfile) {
            $transl = (array) json_decode(file_get_contents($lfile));
            $lang = str_replace(".json", "", basename($lfile));
            $all[$lang] = $code;
            $new_items[$lang] = [];
            foreach ($transl as $key => $value) {
                if (isset($code[$key])) {
                    $all[$lang][$key] = $value;
                }
            }
            if (count($transl) < count($code)) {
                foreach ($code as $key => $value) {
                    if (!isset($transl[$key])) {
                        $new_items[$lang][] = $key;
                    }
                }
            }
        }
        return [
            "strings" => $all,
            "new_items" => $new_items,
            "variables" => $vars
        ];
    }
}