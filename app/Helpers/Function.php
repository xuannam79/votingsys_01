<?php
/**
 * Upload Image
 *
 * @param  string $oldImage
 *
 * @return mixed
 */
function uploadImage($image, $path, $oldImage = null)
{
    if ($image) {
        //move directory folder image
        $img = Image::make($image);

        // Get Extension Image
        $extensionImg = is_string($image) ? getExtension($img->mime()) : $image->getClientOriginalExtension();
        $fileName = uniqid(time(), true) . '.' . $extensionImg;

        if (!$fileName) {
            return null;
        }

        $pathImage = trim($path, '/') . '/' . $fileName;
        $img->save($pathImage);

        //delete old image for update image2wbmp(image)
        if (!empty($oldImage)) {
            deleteImage($path, $oldImage);
        }

        return $fileName;
    }

    return null;
}

/**
 * Delete Image
 * @param  string $path
 * @param  string $nameFile
 *
 * @return mixed
 */
function deleteImage($path, $nameFile)
{
    //check file exists
    if (file_exists($imageDestinationPath = public_path() . $path . $nameFile) && !preg_match('#default#', $nameFile)) {
        File::delete($imageDestinationPath);
    }

    return false;
}

/**
 * Get extension image by mine type
 */
function getExtension($mimeType)
{
    $extensions = [
        'image/jpeg' => 'jpg',
        'image/gif' => 'gif',
        'image/png' => 'png',
        'image/bmp' => 'bmp',
        'image/vnd.microsoft.icon' => 'ico',
    ];

    return isset($extensions[$mimeType]) ? $extensions[$mimeType] : '';
}

/**
 * Check string if is date
 */
function validateDate($date)
{
    try {
        $pattern = '/^(0[1-9]|[1-9]|[1-2][0-9]|3[0-1])[\/-](0[1-9]|[1-9]|1[0-2])[\/-][0-9]{4}\s*(([01]?[0-9]|2[0-3]):[0-5][0-9]){0,1}$/';

        if (!preg_match($pattern, $date)) {
            return false;
        }

        $date = str_replace('/', '-', $date);

        $objectDate = new DateTime($date);

        return [
            'monthYear' => $objectDate->format('F Y'),
            'day' => $objectDate->format('D d'),
            'hour' => $objectDate->format('H:i'),
        ];
    } catch (Exception $e) {
        return false;
    }
}

function existSetting($page, $keyConfig, $setting, $needleKey)
{
    return (
        ($page == 'edit' || $page == 'duplicate')
        && array_key_exists($keyConfig, $setting)
        && array_key_exists($needleKey, $setting));
}

function cleanText($text, $html = true)
{
    if ($text == '') {
        return '';
    }

    $text = nl2br($text, false);

    $arrText = explode('<br>', $text);

    foreach ($arrText as $key => $val) {
        $val = trim($val);
        $val = stripslashes($val);
        $val = htmlspecialchars($val);
        $arrText[$key] = $val;
    }

    if ($html) {
        return implode('<br>', $arrText);
    }

    return implode('\r\n', $arrText);
}

// Validate Setting
function settingsRules()
{
    $settings = config('settings.setting');

    $rules = [];

    $required = [$settings['required_email'], $settings['required_name'], $settings['required_name_and_email']];

    foreach (array_values($settings) as $key) {
        switch ($key) {
            case $settings['required']:
                $rules[] = [
                    'setting.' . $key,
                    'in:' . $key,
                    'required_with:setting_child.' . $key,
                ];

                $rules[] = [
                    'setting_child.' . $key,
                    'in:' . implode(',', $required),
                    'required_with:setting.' . $key,
                ];

                break;

            case $settings['custom_link']:
            case $settings['set_limit']:
            case $settings['set_password']:
                $rules[] = [
                    'setting.' . $key,
                    'in:' . $key,
                    'required_with:value.' . $key,
                ];

                if ($settings['custom_link'] == $key) {
                    $rules[] = [
                        'value.' . $key,
                        'string',
                        'unique:links,token',
                        'required_with:setting.'. $key,
                        'max:255',
                    ];
                } else if ($settings['set_limit'] == $key) {
                    $rules[] = [
                        'value.' . $key,
                        'numeric',
                        'required_with:setting.' . $key,
                    ];
                } else {
                    $rules[] = [
                        'value.' . $key,
                        'string',
                        'required_with:setting.' . $key,
                        'max:255',
                    ];
                }

                break;

            default:
                if (!in_array($key, $required)) {
                    $rules[] = [
                        'setting.' . $key,
                        'in:' . $key,
                    ];
                }

                break;
        }
    }

    $rules = collect($rules)->reduce(function ($lookup, $value) {
        $lookup[$value[0]] = implode('|', array_filter($value, function ($k) {
            return $k;
        }, ARRAY_FILTER_USE_KEY));

        return $lookup;
    });

    return $rules;
}
