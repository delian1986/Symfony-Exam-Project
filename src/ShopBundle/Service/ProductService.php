<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\Product;
use ShopBundle\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class ProductService implements ProductServiceInterface
{
    const IMGUR_CLIENT_ID = "6d208ccd5a9275b";
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(ProductRepository $productRepository, FlashBagInterface $flashBag)
    {
        $this->productRepository = $productRepository;
        $this->flashBag = $flashBag;
    }

    /**
     * @param Product $product
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveProduct(Product $product): void
    {
        $this->productRepository->save($product);

        $this->flashBag->add('success', "{$product->getName()} successfully saved!");
    }

    public function handleImage(string $image): string
    {
//        $imageResource=$this->addWatermark($image);
//        var_dump($imageResource); exit();

        $handle = fopen($image, "r");
        $data = fread($handle,filesize($image));

        $pvars = array('image' => base64_encode($data));
        $timeout = 30;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://api.imgur.com/3/image.json');
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Client-ID ' . self::IMGUR_CLIENT_ID));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $pvars);
        $out = curl_exec($curl);
        curl_close($curl);
        $pms = json_decode($out, true);

        return $pms['data']['link'];
    }

    public function addWatermark($image)
    {
        // Load the stamp and the photo to apply the watermark to
        $stamp = imagecreatefrompng(__DIR__.'\app\Resources\watermark\Untitled.png');
        $im = imagecreatefromjpeg($image);

        // Set the margins for the stamp and get the height/width of the stamp image
        $marge_right = 10;
        $marge_bottom = 10;
        $sx = imagesx($stamp);
        $sy = imagesy($stamp);

        // Copy the stamp image onto our photo using the margin offsets and the photo
        // width to calculate positioning of the stamp.
        imagecopy($im, $stamp, imagesx($im) - $sx - $marge_right, imagesy($im) - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp));

        // Output and free memory
        header('Content-type: image/png');
        imagepng($im);
        imagedestroy($im);

        return $im;

    }
}