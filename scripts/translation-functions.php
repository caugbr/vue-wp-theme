<?php

class TranslationFunctions {

    var $vuewp_theme_dir;
    var $vuewp_app_dir;

    public function __construct($vuewp_theme_dir, $vuewp_app_dir) {
        $this->themeDir = $vuewp_theme_dir;
        $this->appDir = $vuewp_app_dir;
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
            <select name="langs" id="langs" class="half">
                <?php foreach ($languages as $lcode => $lname) { ?>
                    <option value="<?php print $lcode; ?>"><?php print $lname; ?></option>
                <?php } ?>
            </select>
            <button type="button" class="button" id="edit_button"><?php _e('Edit', 'vuewp'); ?></button>
        </div>
    
        <div class="formline">
            <label for="new_code"><?php _e('Create a new language file', 'vuewp'); ?></label>
            <input class="half" type="text" name="new_code" id="new_code" placeholder="<?php _e('Language code', 'vuewp'); ?>">
            <input class="half" type="text" name="new_name" id="new_name" placeholder="<?php _e('Language name', 'vuewp'); ?>">
            <button type="button" class="button" id="create_button"><?php _e('Create', 'vuewp'); ?></button>
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
                    <input type="hidden" id="action" name="action">
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
                        <?php $new_item = in_array($key, $lang_info['new_items'][$lng]); ?>
                        <div class="str-line<?php if ($new_item) print ' not-saved' ?>">
                            <span class="key"><?php print $key; ?></span>
                            <span class="val"><?php print $val; ?></span>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
        <?php
    }
    
    public function create_file($lang) {
        $path = get_template_directory() . $this->appDir . '/src/I18n/langs/' . $lang . '.json';
        if (!file_exists($path)) {
            $fh = fopen($path, "w");
            fclose($fh);
        }
    }
    
    public function save_strings($lang, $json) {
        $path = get_template_directory() . '/' . $this->appDir . '/src/I18n/langs/' . $lang . '.json';
        $valid = json_decode(stripslashes($json), true);
        if (!is_array($valid)) {
            return false;
        }
        $str = json_encode($valid, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return !!file_put_contents($path, $str);
    }
    
    public function read_strings() {
        $app_dir = $this->themeDir . '/' . $this->appDir;
        $lang_files = listFiles($app_dir . '/src/I18n/langs');
        $components = listFiles($app_dir . '/src/components');
        $views = listFiles($app_dir . '/src/views');
        $files = array_merge($components, $views);
    
        $code = ["language_name" => ""];
        foreach ($files as $file) {
            $content = file_get_contents($file);
            preg_match_all("/\bt[pl]? *\([\'\"]([^\'\"]+)[\'\"][^)]*\)/", $content, $matches);
            foreach ($matches[1] as $str) {
                $code[$str] = "";
            }
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
            "new_items" => $new_items
        ];
    }
}