<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\Produk;
use Illuminate\Support\Facades\Log;

class ProcessProductImages extends Command
{
    protected $signature = 'products:process-images';
    protected $description = 'Download, convert, resize product images';

    public function handle()
    {
        $this->info('Memulai proses image processing...');
        
        $products = Produk::where('image_url', 'like', 'http%')->get();
        
        $this->info('Jumlah produk dengan URL eksternal: ' . $products->count());
        
        if ($products->count() == 0) {
            $this->warn('Tidak ada produk dengan URL eksternal ditemukan!');
            return 0;
        }

        $manager = new ImageManager(new Driver());
        $processed = 0;
        $failed = 0;

        foreach ($products as $product) {
            try {
                $this->info("Processing: {$product->nama_produk}");
                $this->info("URL asli: {$product->image_url}");
                
                $response = Http::get($product->image_url);
                
                if (!$response->successful()) {
                    $this->error("Gagal download: " . $product->image_url);
                    $failed++;
                    continue;
                }
                
                $filename = Str::slug($product->nama_produk) . '_' . time() . '.jpg';
                $this->info("Filename: {$filename}");
                
                $image = $manager
                    ->read($response->body())
                    ->resize(800, 800, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->toJpeg(85);
                
                $path = "products/{$filename}";
                Storage::disk('public')->put($path, $image);
                
                $product->update([
                    'image_url' => $filename
                ]);
                
                $processed++;
                $this->info("✓ Berhasil: {$filename}");
                
            } catch (\Exception $e) {
                $this->error("Error processing {$product->nama_produk}: " . $e->getMessage());
                Log::error('Image processing error: ' . $e->getMessage());
                $failed++;
            }
        }

        $this->info("Selesai! Berhasil: {$processed}, Gagal: {$failed}");
        return 0;
    }
}