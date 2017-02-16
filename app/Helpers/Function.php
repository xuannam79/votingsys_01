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
        //set unique name avatar
        $fileName = uniqid(time()) . '.' . $image->getClientOriginalExtension();

        //move directory folder image
        $img = Image::make($image);
        $pathImage = $path . '/' . $fileName;
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
