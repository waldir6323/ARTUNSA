<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (class_exists('Conexion') != true) {

    class Conexion {

        /*private $servername = "www.db4free.net";
        private $usernameBD = "armandofch";
        private $usernamePe = "afloreschoq";
        private $password = "armando123";
        private $dbname = "asistenciabd";
        private $dbnamePermiso = "asistenciapermis";
        private $conn = null;*/

        private $servername = "localhost";
        private $usernameBD = "politiqu_p1";
        private $usernamePe = "politiqu_p1";
        private $password = "rtcltr_1ss";
        private $dbname = "politiqu_dbhhaqp";
        private $dbnamePermiso = "politiqu_bdpoliticos";
        private $conn = null;

        public function __construct() {
            
        }

        public function getConexion() {
            // Create connection
            if ($this->conn == null) {
                $this->conn = new mysqli($this->servername, $this->usernameBD, $this->password, $this->dbname);
                if ($this->conn->connect_error) {
                    die("Connection failed: " . $this->conn->connect_error);
                }
            }

            return $this->conn;
        }

        public function getConexionPermiso() {
            // Create connection
            if ($this->conn == null) {
                $this->conn = new mysqli($this->servername, $this->usernameBD, $this->password, $this->dbname);
                if ($this->conn->connect_error) {
                    die("Connection failed: " . $this->conn->connect_error);
                }
            }

            return $this->conn;
        }

    }

}