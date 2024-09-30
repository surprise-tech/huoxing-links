<?php

namespace App\Console\Commands;

use App\Models\Domain;
use App\Models\Link;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Symfony\Component\HttpFoundation\Response;

class ChkLinkStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:chk-link {link_id?}';

    /**
     * The console command desc.
     *
     * @var string
     */
    protected $desc = 'links URL状态检测';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $link_id = $this->argument('link_id');
        if ($link_id) {
            $this->che_link($link_id);
        } else {
            $this->com_default();
        }

        return Command::SUCCESS;
    }

    private function com_default(): void
    {
        Link::query()
            ->where('status', 1)
            ->where(function ($query) {
                $query->where('expired_at', '<', now());
            })
            ->update(['status' => 0]);
        Domain::query()
            ->where('enable', true)
            ->chunk(100, function ($list) {
                foreach ($list as $item) {
                    $code = $this->chk_url($item->url);
                    if ($code == Response::HTTP_OK) {
                        // 本来就 200 不做更改
                        Link::query()
                            ->where('status', 0)
                            ->whereJsonContains('config->domain_id', $item->id)
                            ->where('expired_at', '>', now())
                            ->update(['status' => 1]);
                    } else {
                        Link::query()
                            ->where('status', 1)
                            ->whereJsonContains('config->domain_id', $item->id)
                            ->where('expired_at', '>', now())
                            ->update(['status' => 0]);
                    }
                }
            });
    }

    private function che_link($link_id): void
    {
        $link = Link::query()->find($link_id);
        $domain_id = data_get($link, 'config.domain_id');
        if ($domain_id) {
            $domain = Domain::query()->find($domain_id);
            if ($domain->url) {
                $code = $this->chk_url($domain->url);
                if ($code == Response::HTTP_OK) {
                    if (! $link->status) {
                        $link->update(['status' => 1]);
                    }
                } else {
                    if ($link->status) {
                        $link->update(['status' => 0]);
                    }
                }
            }
        }
    }

    private function chk_url($url)
    {
        $client = new Client([
            'headers' => [
                'verify' => false,
            ],
        ]);
        try {
            $rs = $client->head("{$url}?code=1234");
            $code = $rs->getStatusCode();
        } catch (\Exception $e) {
            $code = $e->getCode();
        }

        return $code;
    }
}
