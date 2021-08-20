<?php
class GPD_Moeda
{
    public $nome;
    public $nome_plural;
    public $artigo;

    function __construct() {
        $nome = gpd_get_option('gpd_single_currency_name');
        $nome_plural = gpd_get_option('gpd_plural_currency_name');
        $artigo = gpd_get_option('gpd_currency_gender');
        $this->nome = empty($nome) || is_null($nome) ? 'ponto' : strtolower($nome);
        $this->nome_plural = empty($nome_plural) || is_null($nome_plural) ? $this->nome . 's' : strtolower($nome_plural);
        $this->artigo = empty($artigo) || is_null($artigo) ? 'o' : $artigo;
    }

}
