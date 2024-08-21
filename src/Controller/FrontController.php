<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;
use App\Repository\VoucherRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FrontController extends AbstractController{

    #[Route('/', name: 'app_front')]
    public function list(ProductRepository $productRepository, VoucherRepository $voucherRepository, SessionInterface $session): Response
    {
        // Minden termék lekérdezése az adatbázisból név szerint növekvő sorrendben
        $products = $productRepository->findBy(array(),array('name' => 'ASC'));
        
        foreach($products as $product){

            $vouchers = $product->getVoucher() ? $product->getVoucher()['voucher'] : null;

            if(!is_null($vouchers)){
                
                $product->setDiscountedPrices($voucherRepository);
                $product = $product->getDiscountedPrices();
                
            }
        }

        return $this->render('front_page.html.twig', [
            'products' => $products,
            'cart' => $session->get('cart'),
        ]);
    }

}