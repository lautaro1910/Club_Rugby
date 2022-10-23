<?php

namespace App\Controllers;

use Config\Services;
use CodeIgniter\Controller;
use App\Models\socioModel;
use App\Models\juvenilModel;
use App\Models\veteranoModel;

class SocioController extends BaseController
{
    public function __construct()
    {
        helper('form');
        $session = Services::session();
    }

    public function create()
    {
        $request = Services::request();
        $socioModel = new socioModel($db);
        $juvenilModel = new juvenilModel($db);
        $veteranoModel = new veteranoModel($db);
        $data = array(
            'nombre_apellido' => "",
            'dni' => "",
            'telefono' => "",
            'direccion' => "",
            'mail' => "",
            'nombre_tutor' => "",
            'dni_tutor' => "",
        );
        if (strtolower($this->request->getMethod()) !== 'post') {
            $data['validation'] = $this->validator;
            return view('socioView\createSocioView', $data);
        }
        $dataSocio = array(
            'nombre_apellido' => $request->getPost('inputNombre'),
            'dni' => $request->getPost('inputDocumento'),
            'telefono' => $request->getPost('inputTelefono'),
            'direccion' => $request->getPost('inputDireccion'),
            'mail' => $request->getPost('inputMail'),
            'id_categoria' => $request->getPost('selectCategoria'),
        );
        $dataJuvenil = array(
            'nombre_tutor' => $request->getPost('inputNombreTutor'),
            'dni_tutor' => $request->getPost('inputDocumentoTutor'),
        );
        $enfermedad = $request->getPost('inputEnfermedad');
        if ($enfermedad == null) {
            $enfermedad = false;
        } else {
            $enfermedad = true;
        }
        $dataVeterano = array(
            'enfermedad_coronaria' => $enfermedad,
        );
        //$rules = $socioModel->getValidationRules();
        //if (!$this->validate($rules)) {
        //    $data['validation'] = $this->validator;
        //    return view('components\header') . view('components\navbar') . view('bancoView\createBancoView', $data);
        //}
        if (!$socioModel->insert($dataSocio)) {
            var_dump($socioModel->errors());
            $dataSocio['validation'] = $this->validator;
            return view('socioView\createSocioView', $dataSocio);
        }
        $socio = $socioModel->where('dni', $request->getPost('inputDocumento'))->findAll();
        $dataJuvenil['id_socio'] = $socio[0]['id_socio'];
        $dataVeterano['id_socio'] = $socio[0]['id_socio'];
        if ($request->getPost('selectCategoria') == 1) {
            if (!$juvenilModel->insert($dataJuvenil)) {
                $socioModel->delete();
                var_dump($juvenilModel->errors());
                $dataSocio['nombre_tutor'] = $dataJuvenil['nombre_tutor'];
                $dataSocio['dni_tutor'] = $dataJuvenil['dni_tutor'];
                $dataSocio['validation'] = $this->validator;
                return view('socioView\createSocioView', $dataSocio);
            }
        }
        if ($request->getPost('selectCategoria') == 4) {
            if (!$veteranoModel->insert($dataVeterano)) {
                var_dump($veteranoModel->errors());
                $dataSocio['nombre_tutor'] = "";
                $dataSocio['dni_tutor'] = "";
                $dataSocio['validation'] = $this->validator;
                return view('socioView\createSocioView', $dataSocio);
            }
        }
        return view('homeView');
    }

    public function mostrar()
    {
        $socioModel = new socioModel($db);
        if (strtolower($this->request->getMethod()) !== 'post') {
            return view('socioView\mostrarSocioView', [
                'socios' => $socioModel->findAll(),
            ]);
        }
    }

    // public function delete($id)
    // {
    //     $bancoModel = new bancoModel($db);
    //     $bancoModel->where('id_banco', $id)->delete();
    //     $data = $bancoModel->findAll();
    //     return view('components\header') . view('components\navbar') . view('bancoView\mostrarBancoView', [
    //         'validation' => $this->validator,
    //         'bancos' => $data,
    //     ]);
    // }

    // public function update($id)
    // {

    //     $request = \Config\Services::request();
    //     $bancoModel = new bancoModel($db);
    //     $banco = $bancoModel->where('id_banco', $id)->findAll();
    //     $banco = $banco[0];
    //     if (strtolower($this->request->getMethod()) !== 'post') {
    //         $banco['pantalla'] = 'update';
    //         $banco['validation'] = $this->validator;
    //         return view('components\header') . view('components\navbar') . view('bancoView\createBancoView', $banco);
    //     }
    //     $data = array(
    //         'nombre' => $request->getPost('inputNombre'),
    //         'direccion' => $request->getPost('inputDireccion'),
    //         'numero_sucursal' => $request->getPost('inputNroSucursal'),
    //     );
    //     if (!$bancoModel->update($id, $data)) {
    //         var_dump($bancoModel->errors());
    //         $data['validation'] = $this->validator;
    //         $data['pantalla'] = 'update';
    //         return view('components\header') . view('components\navbar') . view('bancoView\createBancoView', $data);
    //     }
    //     return view('components\header') . view('components\navbar') . view('components\operacionExitosa');
    // }

}
