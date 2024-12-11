<?php

namespace App\Repositories;

use App\Interfaces\TransactionRepositoryInterface;
use App\Models\Room;
use App\Models\Transaction;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function getTransactionDataFromSession()
    {
        return session()->get('transaction');
    }

    public function saveTransactionDataFromSession($data)
    {
       $transaction = session()->get('transaction',[]);

        foreach($data as $key => $value){
            $transaction[$key] = $value;
        }
        session()->put('transaction',$transaction);
    }

    public function saveTransaction($data)
    {
        $room = Room::find($data['room_id']);

        $data = $this->prepareTransactionData($data,$room);

        $transaction = Transaction::create($data);

        session()->forget('transaction');

        return $transaction;
    }

    public function getTransactionByCode($code)
    {
        return Transaction::where('code',$code)->first();
    }

    public function getTransactionByCodeEmailPhone($code, $email, $phone)
    {
        return Transaction::where('code',$code)->where('email',$email)->where('phone_number',$phone)->first();
    }
    public function prepareTransactionData($data,$room)
    {
        $data['code'] = $this->generateTransactionCode();
        $data['payment_status'] = 'pending';
        $data['transaction_date'] = now();

        $total = $this->calculateToAmount($room->price_per_month, $data['duration']);
        $data['total_amount'] = $this->calculateToPaymentAmount($total,$data['payment_method']);

        return $data;
    }

    public function generateTransactionCode()
    {
        return 'RHZ' . rand(100000,999999);
    }

    public function calculateToAmount($pricePerMonth, $duration)
    {
        $subtotal = $pricePerMonth * $duration;
        $tax = $subtotal * 0.11;
        $insurance = $subtotal * 0.01;
        return $subtotal + $tax + $insurance;
    }

    public function calculateToPaymentAmount ($total,$paymentMethod)
    {
        return $paymentMethod === 'full_payment' ? $total : $total * 0.3;
    }
}
