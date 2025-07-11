<?php

namespace sm\bounty\bounty;

class Bounty 
{
    private string $player;
    private int $price = 0;
    private string $sender;
    
    public function __construct(string $player, array $data)
    {
        $this->player = $player;
        $this->price = $data['price'];
        $this->sender = $data['sender'];
    }
    
    public function getPlayer(): string
    {
        return $this->player;
    }

    public function getSender(): string
    {
        return $this->sender;
    }
    
    public function getPrice(): string
    {
        return $this->price;
    }
    
    public function getData(): array
    {
        $data = [
            'price' => $this->getPrice(),
            'sender' => $this->getSender(),
        ];
        return $data;
    }
}