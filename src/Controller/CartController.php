<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProductRepository;
use App\Repository\VoucherRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;


class CartController extends AbstractController
{

    #[Route('/cart', name: 'app_cart')]
    public function index(SessionInterface $session,VoucherRepository $voucherRepository): Response
    {
        $cart = $session->get('cart');
        $sum = $this->getDiscountedSum($cart, $voucherRepository);

        $savedCar = $session->get('saved_cart') ? true : false;

        return $this->render('cart/cart.html.twig', [
            'cart' => $cart,
            'sum' => $sum,
            'savedCart' => $savedCar
        ]);
    }

    #[Route('/cart/{id}', name: 'add_cart', requirements: ['id' => '\d+'])]
    public function addCart($id, ProductRepository $productRepository, SessionInterface $session, VoucherRepository $voucherRepository, Request $request): JsonResponse{
        
        $action = $request->query->get('action');

        $productExists = false;

        $cart = $session->get('cart', []);

        if(!empty($cart)){
            
            foreach($cart as $key => &$item){
                
                if($item['product']->getID() == $id){

                    if($action === 'add'){
                        $item['quantity'] += 1;
                        $productExists = true;

                    }elseif ($action === 'remove'){
                        $item['quantity'] -= 1;
                        if($item['quantity'] == 0){
                            unset($cart[$key]);
                        }
                    }
                    
                    break; 
                }
            }
        }
        
        if(!$productExists && $action == 'add'){
            $product = $productRepository->findOneBy(['id' => $id]);
            $product->setDiscountedPrices($voucherRepository);
            $cart[] = ['product' => $product, 'quantity' => 1];
        }
        
        $session->set('cart', $cart);

        $sum = $this->getDiscountedSum($cart, $voucherRepository);

        $cartHtml = $this->renderView('cart/cart_block.html.twig', [
            'cart' => $cart
        ]);
        
        
        return new JsonResponse(['success' => TRUE, 'cartHtml' => $cartHtml, 'action' => $action, 'sum' => $sum]);
    }

    public function getDiscountedSum($cart, VoucherRepository $voucherRepository): array {
        $originalSum = 0;
        $discountedSum = 0;
        $discountSum = 0;
        $hasGroupedDiscountedItem = false;
        $voucher = $voucherRepository->findOneBy(['type' => 'group']);
        $voucherId = $voucher->getID();

        //sima kedvezmények
        if($cart){
            foreach($cart as $item){
                $price = $item['product']->getPrice();
                
                $quantity = $item['quantity'];
                $originalSum += $price * $quantity;
                
                $array = $item['product']->getDiscountedPrices();
                
                $productVoucherArray = $item['product']->getVoucher() ?: null;
                if($productVoucherArray){
                    $hasGroupedDiscountedItem = in_array($voucherId, $productVoucherArray);
                }
                
                if($array){
                    for($i = 0; $i < count($array); $i++){
                        $discountSum += $array[$i]['discount'] * $quantity;
        
                        if ($i === count($array) - 1) {
                            $discountedSum += $array[$i]['price'] * $quantity;
                        }
                    }
                }
            }
        }

        //csoportos kedvezmény van-e?
        if($hasGroupedDiscountedItem){

            foreach($voucher as $item){
                $minItems = $item->getConditions()['min_items'];
                $brand = $item->getConditions()['brand'];
                $counter = 0;
    
                $cheapestProduct = null;
                $groupedProducts = [];
                $lowestPrice = PHP_INT_MAX;
    
                foreach($cart as $product){
                    if($product['product']->getBrand() == $brand){
                        $groupedProducts[] = $product['product'];
                        $counter++;
                    }
                }
    
                if($counter >= $minItems){
    
                    foreach($groupedProducts as $product){
    
                        $currentLowest = $product->getPrice();
                        $discounArray = $product->getDiscountedPrices();
    
                        if($product->getDiscountedPrices()){
                            $discount = end($discounArray);
    
                            if($discount['price'] < $currentLowest){
                                $currentLowest = $discount['price'];
                            }
                        }
    
                        if ($currentLowest < $lowestPrice) {
                            $lowestPrice = $currentLowest;
                            $cheapestProduct = $product;
                        }
                    }
    
                    if ($cheapestProduct !== null) {
                        $discountedSum -= $lowestPrice;
                        $discountSum += $lowestPrice;
                    } 
                }
    
            }
        }


        return ['originalSum' => $originalSum, 'discountedSum' => $discountedSum, 'discountSum' => $discountSum];

    }

    #[Route('/cart/save', name: 'save_cart')]
    public function saveCart(SessionInterface $session): JsonResponse{
        $session->set('saved_cart', $session->get('cart'));
        
        return new JsonResponse(TRUE);
    }

    #[Route('/cart/delete', name: 'delete_cart')]
    public function deleteCart(SessionInterface $session, VoucherRepository $voucherRepository): JsonResponse{
        $session->remove('cart');
        $cart = $session->get('cart');
        $sum = $this->getDiscountedSum($cart, $voucherRepository);

        $cartHtml = $this->renderView('cart/cart_block.html.twig', [
            'cart' => null
        ]);

        return new JsonResponse([
            'success' => true,
            'cartHtml' => $cartHtml,
            'sum' => $sum
        ]);
    }

    #[Route('/cart/update', name: 'update_cart')]
    public function updateCart(SessionInterface $session, VoucherRepository $voucherRepository): JsonResponse
    {
        $cart = $session->get('saved_cart');
        $session->set('cart', $cart);
        $sum = $this->getDiscountedSum($cart, $voucherRepository);

        $cartHtml = $this->renderView('cart/cart_block.html.twig', [
            'cart' => $cart,
            'sum' => $sum
        ]);

        return new JsonResponse([
            'success' => true,
            'cartHtml' => $cartHtml,
            'sum' => $sum
        ]);
    }


}
