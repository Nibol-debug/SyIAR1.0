<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\PpdbRegistrationModel;
use CodeIgniter\API\ResponseTrait;

class PpdbController extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $model = new PpdbRegistrationModel();
        $status = $this->request->getGet('status');
        
        $data = $status ? $model->getByStatus($status) : $model->orderBy('created_at', 'DESC')->paginate(20);
        
        return $this->respond(['success' => true, 'data' => $data, 'pager' => $model->pager ?? null]);
    }
    
    public function stats()
    {
        $model = new PpdbRegistrationModel();
        return $this->respond(['success' => true, 'data' => $model->getStats()]);
    }
    
    public function create()
    {
        $rules = [
            'nama_calon'     => 'required|min_length[3]',
            'jenis_kelamin'  => 'required|in_list[L,P]',
            'tanggal_lahir'  => 'required|valid_date',
            'jenjang_daftar' => 'required',
            'email'          => 'required|valid_email',
            'no_telepon'     => 'required',
        ];
        
        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }
        
        $model = new PpdbRegistrationModel();
        $data = $this->request->getJSON(true);
        
        // Handle file uploads
        if ($this->request->getFile('file_kk')?->isValid()) {
            $file = $this->request->getFile('file_kk');
            $data['file_kk'] = $file->store('ppdb/kk');
        }
        
        $id = $model->insert($data);
        $record = $model->find($id);
        
        return $this->respondCreated([
            'success' => true,
            'message' => 'Pendaftaran berhasil disimpan',
            'data'    => ['id' => $id, 'kode_pendaftaran' => $record['kode_pendaftaran'] ?? null]
        ]);
    }
    
    public function updateStatus($id)
    {
        $model = new PpdbRegistrationModel();
        $registration = $model->find($id);
        
        if (!$registration) return $this->failNotFound('Pendaftaran tidak ditemukan');
        
        $json = $this->request->getJSON(true);
        $allowedStatus = ['draft', 'submitted', 'verified', 'accepted', 'rejected'];
        
        if (!in_array($json['status'] ?? '', $allowedStatus)) {
            return $this->fail('Status tidak valid');
        }
        
        $model->update($id, [
            'status_pendaftaran' => $json['status'],
            'catatan_admin'      => $json['catatan'] ?? null
        ]);
        
        return $this->respond(['success' => true, 'message' => 'Status berhasil diupdate']);
    }

    public function convertToSantri($id)
    {
        $model = new PpdbRegistrationModel();
        $registration = $model->find($id);
        
        if (!$registration) return $this->failNotFound('Pendaftaran tidak ditemukan');
        if ($registration['status_pendaftaran'] !== 'accepted') {
            return $this->fail('Hanya pendaftar dengan status Lulus (accepted) yang bisa dijadikan santri');
        }
        
        // Cek apakah sudah jadi santri
        $santriModel = new \App\Models\SantriModel();
        $existing = $santriModel->where('email', $registration['email'])->first();
        if ($existing) return $this->fail('Santri dengan email ini sudah terdaftar');

        // Insert to santris
        $newSantri = [
            'nama_lengkap' => $registration['nama_calon'],
            'jenis_kelamin' => $registration['jenis_kelamin'],
            'tempat_lahir' => $registration['tempat_lahir'] ?? 'Belum Diisi',
            'tanggal_lahir' => $registration['tanggal_lahir'],
            'alamat' => $registration['alamat'] ?? 'Belum Diisi',
            'email' => $registration['email'],
            'no_telepon' => $registration['no_telepon'],
            'status' => 'aktif',
            'tanggal_masuk' => date('Y-m-d')
        ];
        
        $santriId = $santriModel->insert($newSantri);
        
        // Update registration status or catatan
        $model->update($id, [
            'catatan_admin' => 'Sudah dikonversi menjadi santri aktif pada ' . date('Y-m-d H:i:s')
        ]);
        
        return $this->respondCreated([
            'success' => true,
            'message' => 'Berhasil mengonversi pendaftar menjadi santri aktif',
            'data' => ['santri_id' => $santriId]
        ]);
    }
}
