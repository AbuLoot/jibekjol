<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;

use Validator;

use App\Models\User;
use App\Models\App;
use App\Models\Track;
use App\Models\Project;
use App\Models\ProjectIndex;
use App\Models\Product;
use App\Models\Category;
use App\Models\Section;
use App\Mail\CompaniesMerged;


class InputController extends Controller
{
    public function search(Request $request, $lang)
    {
        $text = Str::upper(trim(strip_tags($request->text)));

	    // $products = Product::where('status', 1)
	    //     ->where(function($query) use ($text, $qQuery) {
	    //         return $query->where('barcode', 'LIKE', '%'.$text.'%')
	    //         ->orWhere('title', 'LIKE', '%'.$text.'%')
	    //         ->orWhere('oem', 'LIKE', '%'.$text.'%');
	    //     })->paginate(27);

        $text = $this->searchByLatin($text);

        $products = Product::search($text)->paginate(28);

        // $products = Product::where('status', '<>', 0)->searchable($text)->paginate(28);

        $products->appends([
            'text' => $request->text,
        ]);

        return view('found', compact('text', 'products'));
    }

    public function searchTrack(Request $request, $lang)
    {
        $code = trim(strip_tags($request->code));

        $tracks = Track::where('code', $code)->get();

        return view('track-page', compact('code', 'tracks'));
    }

    public function searchAjax(Request $request, $lang)
    {
        $text = Str::upper(trim(strip_tags($request->text)));

        $text = $this->searchByLatin($text);

        $products = Product::search($text)->take(100)->get();

        return view('suggestions-render', ['products' => $products]);
    }

    public function searchByLatin($text)
    {
        $words = explode(' ', $text);

        foreach($words as $key => $word) {

            $project_index = ProjectIndex::search($word)->first();

            if ($project_index) {
                $words[$key] = $project_index->original;
                break;

                /*if (in_array($project_index->title, $words)) {
                    $words[$key] = $project_index->original;
                    break;
                } else {
                    $new_text = str_ireplace($project_index->original, $word, $text);
                    break;
                }*/
            }
        }

        return implode(' ', $words);
    }

    public function filterProducts(Request $request)
    {
        $from = ($request->price_from) ? (int) $request->price_from : 0;
        $to = ($request->price_to) ? (int) $request->price_to : 9999999999;

        $products = Product::where('status', 1)->whereBetween('price', [$request->from, $request->to])->paginate(27);

        return redirect()->back()->with([
            'alert' => $status,
            'products' => $products
        ]);
    }

    public function calculate(Request $request, $lang)
    {
        $validator = Validator::make($request->all(), [
            'length' => 'required|numeric|min:2|max:10',
            'width' => 'required|numeric|min:2|max:10',
            'height' => 'required|numeric|min:2|max:10',
            'weight' => 'required|numeric|min:2|max:10',
            'type_delivery' => 'required|numeric',
        ]);

        $typesDelivery = [
            '1' => 'standart-price',
            '2' => 'express-price',
            '3' => 'express-price-clothes',
        ];
        $typeDelivery = $typesDelivery[$request->type_delivery];

        $priceList = Section::where('slug', $typeDelivery)->first();
        $densityPrice = unserialize($priceList->data);

        $length = (float) $request->length;
        $width  = (float) $request->width;
        $height = (float) $request->height;
        $weight = (float) $request->weight;

        $amount = $length * $width * $height;
        $density = (int) round($weight / $amount);

        foreach ($densityPrice as $key => $value) {

            $densityRange = explode('-', $value['key']);
            $range = ['min_range' => $densityRange[0], 'max_range' => $densityRange[1]??null];
            $options = ['options' => $range];

            if (filter_var($density, FILTER_VALIDATE_FLOAT, $options) == true) {

                return redirect()->back()->with([
                        'price' => $value['value'],
                        'density' => $density,
                        'densityRange' => $densityRange,
                        'length' => $length,
                        'width' => $width,
                        'height' => $height,
                        'weight' => $weight,
                        'typeDelivery' => $request->type_delivery,
                    ]);
            }

            if (!isset($densityRange[1])) {

                if ((in_array($densityRange[0], ['800', '1000']) && $density > $densityRange[0]) || 
                    (in_array($densityRange[0], ['100']) && $density < $densityRange[0])) {

                    return redirect()->back()->with([
                        'price' => $value['value'],
                        'density' => $density,
                        'densityRange' => $densityRange,
                        'length' => $length,
                        'width' => $width,
                        'height' => $height,
                        'weight' => $weight,
                        'typeDelivery' => $request->type_delivery,
                    ]);
                }
            }
        }

        return redirect()->back();
        // dd($amount, $density, $densityPrice, $typeDelivery, $request->all());
    }

    public function notifyAboutMerged()
    {
        $users = User::where('created_at', null)->where('updated_at', null)->get();

        $i = 1;

        foreach ($users as $user) {
            Mail::to($user->email)->send(new CompaniesMerged($user));
            $user->updated_at = now();
            $user->save();
            $i++;
        }

        echo $i.' Done!';
    }

    public function unsubscribe($lang, $token, $id)
    {
        $email = Crypt::decryptString($token);

        $user = User::where('id', $id)->where('email', $email)->first();
        $user->status = 2;
        $user->save();

        return redirect()->back();
    }

    public function unsubscribeDone()
    {
        return view('unsubscribe-page');
    }

    public function unsubscribeEmails()
    {
        $fh = fopen('file-manager/incorrect-emails.txt', 'r');

        $emails = [];

        while ($line = fgets($fh)) {
            $emails[] = trim($line);
        }

        fclose($fh);

        $users = User::whereIn('email', $emails)->get();

        foreach ($users as $key => $user) {
            $user->status = 2;
            $user->save();
        }
    }

    public function sendApp(Request $request, $lang)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:2|max:60',
            'phone' => 'required|min:5',
        ]);

        // $domainMail = strrchr($request->email, '@');
        // $emails = ['gmail.com', 'mail.ru', 'inbox.ru', 'bk.ru', 'list.ru', 'yandex.ru', 'icloud.com', 'tutanota.com'];
        // (!empty($request->surname) && !in_array($domainMail, $emails))

        // For spam
        if (!empty($request->surname)) {
            return redirect()->back();
        }

        if ($validator->fails()) {
            return redirect()->withErrors($validator)->withInput();
        }

        $app = new App;
        $app->name = $request->name;
        $app->email = $request->email;
        $app->phone = $request->phone;
        $app->message = $request->message;
        $app->save();

        $status = 'Ваша заявка принята. Спасибо!';

        // dd($status, $message);
        return redirect()->back()->with([
            'status' => $status
        ]);
    }
}