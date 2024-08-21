<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\VoucherRepository;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\Column(length: 255)]
    private ?string $brand = null;

    #[ORM\Column(nullable: true)]
    private ?array $voucher = null;

    private ?array $discountedPrices = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getVoucher(): ?array
    {
        return $this->voucher;
    }

    public function setVoucher(?array $voucher): static
    {
        $this->voucher = $voucher;

        return $this;
    }

    public function getDiscountedPrices(): ?array
    {
        return $this->discountedPrices;
    }

    public function setDiscountedPrices(VoucherRepository $voucherRepository)
    {
        $vouchers = $this->getVoucher();
        if($vouchers){
            $price = $this->getPrice(); 
            foreach($vouchers['voucher'] as $voucher){
                $voucher_object = $voucherRepository->findOneBy(['id' => $voucher]);
                
                if($voucher_object && $voucher_object->getType() !== 'group'){ //a csoportos kedvezményt nem itt számoljuk
    
                    $type = $voucher_object->getType();
                    $conditions = $voucher_object->getConditions();
                    
                    switch($type){
    
                        case 'price':
                            $discount = $conditions['fixed'];
                            $price = $price - $discount;
                            break;
    
                        case 'percentage':
                            $discount = ($price / 100) * $conditions['percent']; //a kedvezmény mértéke
                            $price = $price - $discount;
                            break;
                    }
                    $this->discountedPrices[] = ['price' => $price, 'discount' => $discount];
                }
            }
        }

    }
}
