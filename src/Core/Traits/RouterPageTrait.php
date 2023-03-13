<?php

namespace Edalicio\SimpleRouter\Core\Traits;

use Edalicio\SimpleRouter\Core\Enums\HttpMethodsEnum;

trait RouterPageTrait
{
    private array $files = [];
    private string $basePath;


    public function pages($path)
    {
        $this->basePath = $path;


        $this->getFiles($path);
               
        
        foreach ($this->files as $key => $value) {

            $uri =  "/page" . $value['page'];

            $this->get($uri, function($args) use($value) {
                extract($args, EXTR_SKIP);
                require( $value['file']);
            });      

        }


        // dd($this->routes);
    }

    private function getFiles($path)
    {
        $diretorio = scandir($path);
        unset($diretorio[0]);
        unset($diretorio[1]);
        $diretorio = array_values($diretorio);

        foreach ($diretorio as $key => $value) {

            preg_match('~.php~is', $value, $matchesDir);

            if (empty($matchesDir)) {
                unset($this->files[$path . '/' . $value]);
                $this->getFiles($path . '/' . $value);
            }

            $page = $this->replacePage($path,$value);


            if (!empty($matchesDir)) {
                $this->files[$path . '/' . $value] = [
                    'file' => $path . '/' . $value,
                    'page' => $page,

                ];
            }
        }
    }

    public function replacePage($path, $file): string
    {
        $page = str_replace(['index.php', '.php'], '', $file);
        $page = str_replace($this->basePath, '', $path . '/' . $page);

        preg_match_all("~\[(.*?)\]~", $page, $matches);


        return array_reduce($matches[1], function ($p, $r) {
            return preg_replace("~\[$r\]~", ":$r", $p);
        }, $page);
    }
}