<?php

    //Classe dashboard
    class Dashboard{
        public $data_inicio;
        public $data_fim;

        public $numeroVendas;
        public $totalVendas;

        public $clientesAtivos;
        public $clientesInativos;

        public $totalReclamacoes;
        public $totalElogios;
        public $totalSugestoes;

        public $numeroDespesas;
        public $totalDespesas;


        public function __set($name, $value){
            $this->$name = $value;
            return $this;
        }

        public function __get($name){
            return $this->$name;
        }
    }

    //Classe de conexão com  o banco de dados
    class Conexao{
        private $host = 'localhost';
        private $dbname = 'dashboard';
        private $user = 'euller';
        private $pass = '12345';

        public function conectar(){
            try{

                $conexao = new PDO(
                  "mysql:host=$this->host; dbname=$this->dbname",
                  "$this->user",
                    "$this->pass"
                );

                //Reforça a compatibilidade de caracteres entre o front-end o back-end
                $conexao->exec('set charset set  utf-8');

                return $conexao;

            }catch (PDOException $e){
                echo '<p>'.$e->getMessage().'</p>';
            }
        }
    }


    //Classe (model)
    class Bd{
        private $conexao;
        private $dashboard;

        public function __construct(Conexao $conexao, Dashboard $dashboard){
            $this->conexao = $conexao->conectar();
            $this->dashboard = $dashboard;
        }

        public function getNumeroVendas(){
            $query = "
                        select 
                             count(*) as numero_vendas 
                        from 
                             tb_vendas 
                        where
                             data_venda
                        between 
                             :data_inicio and :data_fim";
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
            $stmt->bindValue(':data_fim',$this->dashboard->__get('data_fim'));
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->numero_vendas;
        }

        public function getTotalVendas(){
            $query = "
                        select 
                             sum(total) as total_vendas 
                        from 
                             tb_vendas 
                        where
                             data_venda
                        between 
                             :data_inicio and :data_fim";
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
            $stmt->bindValue(':data_fim',$this->dashboard->__get('data_fim'));
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->total_vendas;
        }

        public function getClientesAtivos(){
            $query = "
                        select 
                             count(*) as clientes_ativos 
                        from 
                             tb_clientes 
                        where
                             cliente_ativo = 1";
            $stmt = $this->conexao->prepare($query);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->clientes_ativos;
        }

        public function getClientesInativos(){
            $query = "
                        select 
                             count(*) as clientes_inativos 
                        from 
                             tb_clientes 
                        where
                             cliente_ativo = 0";
            $stmt = $this->conexao->prepare($query);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->clientes_inativos;
        }

        public function getTotalReclamacoes(){
            $query = "
                        select 
                             count(*) as total_reclamacoes 
                        from 
                             tb_contatos
                        where
                             tipo_contato = 1";

            $stmt = $this->conexao->prepare($query);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->total_reclamacoes;
        }

        public function getTotalElogios(){
            $query = "
                        select 
                             count(*) as total_elogios
                        from 
                             tb_contatos
                        where
                             tipo_contato = 2";

            $stmt = $this->conexao->prepare($query);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->total_elogios;
        }

        public function getTotalSugestoes(){
            $query = "
                        select 
                             count(*) as total_sugestoes 
                        from 
                             tb_contatos
                        where
                             tipo_contato = 3";

            $stmt = $this->conexao->prepare($query);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->total_sugestoes;
        }

        public function getNumeroDespesas(){
            $query = "
                        select 
                             count(*) as numero_despesas 
                        from 
                             tb_despesas
                        where
                             data_despesa
                        between 
                             :data_inicio and :data_fim";
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
            $stmt->bindValue(':data_fim',$this->dashboard->__get('data_fim'));
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->numero_despesas;
        }

        public function getTotalDespesas(){
            $query = "
                        select 
                             sum(total) as total_despesas 
                        from 
                             tb_despesas 
                            where
                             data_despesa
                        between 
                             :data_inicio and :data_fim";

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
            $stmt->bindValue(':data_fim',$this->dashboard->__get('data_fim'));
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->total_despesas;
        }

    }

    $conexao = new Conexao();
    $dashboard = new Dashboard();

    $data = explode('-',$_GET['data']);
    $ano = $data[0];
    $mes = $data[1];

    // Essa função retorna a quantidade de dias presentes em um determinado mes de um determinado ano
    $dias_do_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
    $dashboard -> __set('data_inicio', $ano.'-'.$mes.'-01');
    $dashboard -> __set('data_fim', $ano.'-'.$mes.'-'.$dias_do_mes);

    $bd = new Bd($conexao, $dashboard);
    $dashboard->__set('numeroVendas', $bd->getNumeroVendas());
    $dashboard->__set('totalVendas', $bd->getTotalVendas());
    $dashboard->__set('clientesAtivos', $bd->getClientesAtivos());
    $dashboard->__set('clientesInativos', $bd->getClientesInativos());
    $dashboard->__set('totalReclamacoes', $bd->getTotalReclamacoes());
    $dashboard->__set('totalElogios', $bd->getTotalElogios());
    $dashboard->__set('totalSugestoes', $bd->getTotalSugestoes());
    $dashboard->__set('numeroDespesas', $bd->getNumeroDespesas());
    $dashboard->__set('totalDespesas', $bd->getTotalDespesas());

    echo json_encode($dashboard); //Converção de um objeto para uma string json

    ?>
