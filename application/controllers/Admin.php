<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class admin extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('Admin_model');
	}

    public function index()
    {
        $data['pengumumansiswa'] = $this->Admin_model->getPengumumanSiswa()->result();
        $data['pengumumanguru'] = $this->Admin_model->getPengumumanGuru()->result();
        $this->load->view('part/header');
        $this->load->view('part/sidebaradmin');
        $this->load->view('admin/dashboard',$data);
        $this->load->view('part/footer');
    }
    public function dataSiswa($status)
    {
    	$data['siswa']=$this->Admin_model->getDataSiswa($status)->result();
        $this->load->view('part/header');
        $this->load->view('part/sidebaradmin');
        $this->load->view('admin/dataSiswa',$data);
        $this->load->view('part/footer');
    }
    public function dataPengajar($id)
    {
    	$data['pengajar']=$this->Admin_model->view_where('el_pengajar',array('status_id'=>$id))->result();
        $this->load->view('part/header');
        $this->load->view('part/sidebaradmin');
        $this->load->view('admin/dataPengajar',$data);
        $this->load->view('part/footer');
    }
    public function detailSiswa($id)
    {
    	$data['siswa']=$this->Admin_model->view_where('el_siswa',array('id'=>$id))->result();
    	$data['kelas']=$this->Admin_model->getKelasSiswa($id)->result();
    	$data['akun']=$this->Admin_model->getAkunSiswa($id)->result();
        $data['daftarkelas']=$this->Admin_model->view_where('el_kelas',array('aktif'=>1))->result();

        $this->load->view('part/header');
        $this->load->view('part/sidebaradmin');
        $this->load->view('admin/detailSiswa',$data);
        $this->load->view('part/footer');
    }
    public function detailPengajar($id)
    {
        $data['nama'] = $this->session->userdata('nama');
        $data['pengajar']=$this->siswa_model->view_where('el_pengajar',array('id'=>$id))->result();
        $data['jadwal']=$this->siswa_model->jadwalPengajar($id)->result();
        $this->load->view('part/header');
        $this->load->view('part/sidebaradmin');
        $this->load->view('admin/detailPengajar',$data);
        $this->load->view('part/footer');
    }
    public function tambahSiswa()
    {
        $data['kelas']=$this->Admin_model->view_where('el_kelas',array('aktif'=>1))->result();
    	$this->load->view('part/header');
        $this->load->view('part/sidebaradmin');
        $this->load->view('admin/tambahSiswa',$data);
        $this->load->view('part/footer');
    }
    public function tambahPengajar()
    {
        $this->load->view('part/header');
        $this->load->view('part/sidebaradmin');
        $this->load->view('admin/tambahPengajar');
        $this->load->view('part/footer');
    }

    public function profile()
    {
        $data['profile'] = $this->Admin_model->getProfileAdmin($this->session->userdata('id'))->result();
        $this->load->view('part/header');
        $this->load->view('part/sidebaradmin');
        $this->load->view('admin/profile',$data);
        $this->load->view('part/footer');
    }

    public function updateprofile($id)
    {
        $data = array(
            'nip' => $this->input->post('NIP'), 
            'nama' => $this->input->post('Nama'), 
            'jenis_kelamin' => $this->input->post('jk'), 
            'tempat_lahir' => $this->input->post('tempatlahir'), 
            'tgl_lahir' => $this->input->post('tgllahir'), 
            'alamat' => $this->input->post('alamat')
        );
        $this->Admin_model->updateProfile($data,$id);
        redirect('admin/profile');
    }

    
    public function updateGambar()
    {
        $config['upload_path']          = './assets/images/user/';
        $config['allowed_types']        = 'jpg|jpeg|png';

        $this->load->library('upload', $config);
        $upload = $this->upload->do_upload('file-input');
        if (!$upload){
            $data['profile'] = $this->pengajar_model->getProfilePengajar($this->session->userdata('id'))->result();
            $this->session->set_flashdata('error', $this->upload->display_errors());
            
            $this->load->view('part/header');
            $this->load->view('part/sidebarsiswa');
            $this->load->view('siswa/profile',$data);
            $this->load->view('part/footer');
        }else{
            $upload = $this->upload->data();
            $data = array(
                'foto' => $upload['file_name']
            );
            $array = array(
                'foto' => $upload['file_name']
            );
            $this->session->set_userdata( $array );
            $this->pengajar_model->updateImage($data,$this->session->userdata('id'));
            redirect('admin/profile');
        }
    }

    public function updateStatusPengajar($id,$status)
    {
        $this->Admin_model->update(array('status_id'=>$status),array('id'=>$id),'el_pengajar');
        redirect('Admin/dataadmin/'.$status);
    }
    public function updateStatusSiswa($id,$status)
    {
        $this->Admin_model->update(array('status_id'=>$status),array('id'=>$id),'el_siswa');
        redirect('Admin/dataSiswa/'.$status);
    }
    public function Pengumuman()
    {
        $data['pengumuman'] = $this->Admin_model->getPengumuman()->result();
        $this->load->view('part/header');
        $this->load->view('part/sidebaradmin');
        $this->load->view('admin/pengumuman/index',$data);
        $this->load->view('part/footer');
    }

    public function TambahPengumuman()
    {
        $this->load->view('part/header');
        $this->load->view('part/sidebaradmin');
        $this->load->view('admin/pengumuman/TambahPengumuman');
        $this->load->view('part/footer');
    }
    
    public function prosesTambahPengumuman()
    {
        $this->form_validation->set_rules('judul', 'judul', 'required');
        $this->form_validation->set_rules('isi', 'isi', 'required');
        $this->form_validation->set_rules('pengajar', 'pengajar', 'required');
        $this->form_validation->set_rules('siswa', 'siswa', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $data = array(
                'judul' => $this->input->post('judul'),
                'konten' => $this->input->post('isi'),
                'tgl_tampil' => $this->input->post('tanggal'),
                'tgl_tutup' => $this->input->post('tanggal'),
                'tampil_siswa' => $this->input->post('siswa'),
                'tampil_pengajar' => $this->input->post('pengajar'),
                'pengajar_id' => $this->session->userdata('id')                
            );
            $this->Admin_model->TambahPengumuman($data);
            redirect('admin/Pengumuman');
            
        } else {
            $this->session->set_flashdata('alert', $this->User_Model->get_alert('warning', 'lengkapilah form di bawah.'));
            
            redirect('admin/TambahPengumuman');
        }
        
    }

    public function EditPengumuman($id)
    {
        $data['pengumuman'] = $this->Admin_model->getDetailPengumuman($id)->result();
        // print_r($data);
        $this->load->view('part/header');
        $this->load->view('part/sidebaradmin');
        $this->load->view('admin/pengumuman/EditPengumuman', $data);
        $this->load->view('part/footer');
    }
    
    public function prosesEditPengumuman()
    {
        $this->form_validation->set_rules('judul', 'judul', 'required');
        $this->form_validation->set_rules('isi', 'isi', 'required');
        $this->form_validation->set_rules('pengajar', 'pengajar', 'required');
        $this->form_validation->set_rules('siswa', 'siswa', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $id = $this->input->post('id');
            $data = array(
                'judul' => $this->input->post('judul'),
                'konten' => $this->input->post('isi'),
                'tgl_tampil' => $this->input->post('tanggal'),
                'tgl_tutup' => $this->input->post('tanggal'),
                'tampil_siswa' => $this->input->post('siswa'),
                'tampil_pengajar' => $this->input->post('pengajar'),
                'pengajar_id' => $this->session->userdata('id')                
            );
            $this->Admin_model->updatePengumuman($data,$id);
            // print_r($data);
            redirect('admin/Pengumuman');
            
        } else {
            $this->session->set_flashdata('alert', $this->User_Model->get_alert('warning', 'lengkapilah form di bawah.'));
            redirect('admin/EditPengumuman/'.$this->input->post('id'));
        }
        
    }

    public function TampilPengumuman($id)
    {
        $data['pengumuman'] = $this->Admin_model->getDetailPengumuman($id)->result();
        $data['author'] = $this->Admin_model->getPengajar($data['pengumuman'][0]->pengajar_id)->result();
        // print_r($data);
        $this->load->view('part/header');
        $this->load->view('part/sidebaradmin');
        $this->load->view('admin/pengumuman/DetailPengumuman',$data);
        $this->load->view('part/footer');
    }

    public function hapusPengumuman($id)
    {
        $this->Admin_model->hapusPengumuman($id);
        redirect('admin/pengumuman');
    }

    public function Mapel()
    {
        $data['mapel'] = $this->Admin_model->GetAllMapel()->result();
        $this->load->view('part/header');
        $this->load->view('part/sidebaradmin');
        $this->load->view('admin/MataPelajaran/index',$data);
        $this->load->view('part/footer');
    }
    public function TambahMataPelajaran()
    {
        $this->load->view('part/header');
        $this->load->view('part/sidebaradmin');
        $this->load->view('admin/MataPelajaran/addMataPelajaran');
        $this->load->view('part/footer');
    }

    public function prosesTambahMapel()
    {
        $this->form_validation->set_rules('mapel', 'mapel', 'required');
        $this->form_validation->set_rules('deskripsi', 'deskripsi', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $data = array(
                'nama' => $this->input->post('mapel'),
                'info' => $this->input->post('deskripsi'),                
                'aktif' => 1                
            );
            $this->Admin_model->addMataPelajaran($data);
            redirect('admin/Mapel');
        } else {
            $this->session->set_flashdata('alert', $this->User_Model->get_alert('warning', 'lengkapilah form di bawah.'));
            redirect('admin/TambahMataPelajaran');
        }
    }

    public function hapusMataPelajaran($id)
    {
        $this->Admin_model->deleteMapel($id);
        redirect('admin/Mapel');
    }

    public function EditMataPelajaran($id)
    {
        $data['mapel'] = $this->Admin_model->getMapelById($id)->result();
        $this->load->view('part/header');
        $this->load->view('part/sidebaradmin');
        $this->load->view('admin/MataPelajaran/EditMataPelajaran',$data);
        $this->load->view('part/footer');
    }

    public function prosesEditMapel()
    {
        $this->form_validation->set_rules('mapel', 'mapel', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $id = $this->input->post('id');
            $aktif = $this->input->post('aktif');
            if ($aktif == 1) {
                $aktif = 1;
            }else{
                $aktif = 0;
            }
            $data = array(
                'nama' => $this->input->post('mapel'),
                'info' => $this->input->post('deskripsi'),                
                'aktif' => $aktif
            );
            $this->Admin_model->editMapel($data,$id);
            redirect('admin/Mapel');
        } else {
            $this->session->set_flashdata('alert', $this->User_Model->get_alert('warning', 'lengkapilah form di bawah.'));
            redirect('admin/EditMataPelajaran');
        }
    }
    public function filterPengajar()
    {
        $daftarFilter=array();
        $daftarKelamin=array();
        if ($this->input->post()) {
            $daftarKelamin=$this->input->post('jeniskelamin');
            $daftarFilter=array(
                'nip'=>$this->input->post('nip'),
                'nama'=>$this->input->post('nama'),
                'tempat_lahir'=>$this->input->post('tempatLahir'),
                'tgl_lahir'=>$this->input->post('tahun').'-'.$this->input->post('bulan').'-'.$this->input->post('tanggal'),
                'alamat'=>$this->input->post('alamat'),
                'is_admin'=>$this->input->post('opsi')
            );
        }
        $data['nama'] = $this->session->userdata('nama');
        $dataFilter['data']=$this->siswa_model->filterPengajar($daftarFilter,$daftarKelamin)->result();
        $this->load->view('part/header');
        $this->load->view('part/sidebaradmin',$data);
        $this->load->view('admin/filterPengajar',$dataFilter);
        $this->load->view('part/footer');
    }

    public function filterSiswa()
    {
        $daftarFilter=array();
        $daftarKelamin=array();
        $daftarAgama=array();
        $daftarKelas=array();
        if ($this->input->post()) {
            $daftarKelas=$this->input->post('kelas');
            $daftarAgama=$this->input->post('agama');
            $daftarKelamin=$this->input->post('jeniskelamin');
            $daftarFilter=array(
                'nis'=>$this->input->post('nis'),
                'nama'=>$this->input->post('nama'),
                'tahun_masuk'=>$this->input->post('tahunMasuk'),
                'tempat_lahir'=>$this->input->post('tempatLahir'),
                'tgl_lahir'=>$this->input->post('tahun').'-'.$this->input->post('bulan').'-'.$this->input->post('tanggal'),
                'alamat'=>$this->input->post('alamat'),
                'status_id'=>$this->input->post('statusSiswa')
            );
        }
        $data['nama'] = $this->session->userdata('nama');
        // echo "<pre>";
        // print_r($daftarFilter);
        // echo "</pre>";
        // echo "<pre>";
        // print_r($daftarKelas);
        // echo "</pre>";
        // echo "<pre>";
        // print_r($daftarAgama);
        // echo "</pre>";
        // echo "<pre>";
        // print_r($daftarKelamin);
        // echo "</pre>";
        $dataFilter['data']=$this->siswa_model->filterSiswa($daftarFilter,$daftarKelamin,$daftarAgama,$daftarKelas)->result();
        // echo "<pre>";
        // print_r($dataFilter);
        // echo "</pre>";
        $this->load->view('part/header');
        $this->load->view('part/sidebaradmin',$data);
        $this->load->view('admin/filterSiswa',$dataFilter);
        $this->load->view('part/footer');
    }
    public function detailFilterSiswa($id)
    {
        $data['nama'] = $this->session->userdata('nama');
        $data['siswa']=$this->siswa_model->view_where('el_siswa',array('id'=>$id))->result();
        $data['kelas']=$this->siswa_model->getKelasSiswa($id)->result();
        $this->load->view('part/header');
        $this->load->view('part/sidebaradmin',$data);
        $this->load->view('admin/detailFilterSiswa',$data);
        $this->load->view('part/footer');
    }
    public function detailFilterPengajar($id)
    {
        // $this->load->model('siswa_model');
        $data['nama'] = $this->session->userdata('nama');
        $data['pengajar']=$this->siswa_model->view_where('el_pengajar',array('id'=>$id))->result();
        $data['jadwal']=$this->siswa_model->jadwalPengajar($id)->result();
        $this->load->view('part/header');
        $this->load->view('part/sidebaradmin',$data);
        $this->load->view('admin/detailFilterPengajar',$data);
        $this->load->view('part/footer');
    }
    public function simpanSiswa()
    {
        $dataSiswa=array(
                'nis'=>$this->input->post('nis'),
                'nama'=>$this->input->post('nama'),
                'tahun_masuk'=>$this->input->post('tahun_masuk'),
                'tempat_lahir'=>$this->input->post('tempat_lahir'),
                'tgl_lahir'=>$this->input->post('tgl_lahir'),
                'alamat'=>$this->input->post('alamat'),
                'status_id'=>$this->input->post('statusSiswa'),
                'agama'=>$this->input->post('agama'),
                'jenis_kelamin'=>$this->input->post('jenis_kelamin'),
                'status_id'=>0,
                // 'foto'=>
        );
        $return_id=$this->Admin_model->insert($dataSiswa,'el_siswa');
        $dataKelas=array(
            'siswa_id'=>$return_id,
            'kelas_id'=>$this->input->post('kelas_id'),
            'aktif'=>1
        );
        $this->Admin_model->insert($dataKelas,'el_kelas_siswa');
        $dataLogin=array(
            'username'=>$this->input->post('username'),
            'password'=>md5($this->input->post('password')),
            'siswa_id'=>$return_id,
            'is_admin'=>0
        );
        $this->Admin_model->insert($dataLogin,'el_login');
        redirect('Admin/dataSiswa/0');
    }
    public function updateKelasSiswa($idsiswa,$idkelassiswa)
    {
        // echo $idsiswa.','.$idkelassiswa;
        $this->Admin_model->update(array('aktif'=>0),array('id'=>$idkelassiswa),'el_kelas_siswa');
        $this->Admin_model->insert(array('siswa_id'=>$idsiswa,'kelas_id'=>$this->input->post('kelas_id'),'aktif'=>1),'el_kelas_siswa');
        redirect('Admin/detailSiswa/'.$idsiswa);
    }
    public function updateAkunSiswa($akun_id,$idsiswa)
    {
        $dataAkun=array(
            'username'=>$this->input->post('username'),
            'password'=>md5($this->input->post('password'))
        );
        $this->Admin_model->update($dataAkun,array('id'=>$akun_id),'el_login'); 
        redirect('Admin/detailSiswa/'.$idsiswa);
    }
    public function updateSiswa($idsiswa)
    {
        $dataSiswa=array(
            'nis'=>$this->input->post('nis'),
                'nama'=>$this->input->post('nama'),
                'tahun_masuk'=>$this->input->post('tahun_masuk'),
                'tempat_lahir'=>$this->input->post('tempat_lahir'),
                'tgl_lahir'=>$this->input->post('tgl_lahir'),
                'alamat'=>$this->input->post('alamat'),
                'status_id'=>$this->input->post('statusSiswa'),
                'agama'=>$this->input->post('agama'),
                'jenis_kelamin'=>$this->input->post('jenis_kelamin'),
                'status_id'=>1
        );
        $this->Admin_model->update($dataSiswa,array('id'=>$idsiswa),'el_siswa'); 
        redirect('Admin/detailSiswa/'.$idsiswa);
    }
    public function updatePengajar($idpengajar)
    {
        $dataPengajar=array(
                'nip'=>$this->input->post('nip'),
                'nama'=>$this->input->post('nama'),
                'tempat_lahir'=>$this->input->post('tempat_lahir'),
                'jenis_kelamin'=>$this->input->post('jenis_kelamin'),
                'tgl_lahir'=>$this->input->post('tgl_lahir'),
                'alamat'=>$this->input->post('alamat')
        );//print_r($dataPengajar);
        $this->Admin_model->update($dataPengajar,array('id'=>$idpengajar),'el_pengajar'); 
        redirect('Admin/detailadmin/'.$idpengajar);
    }
    public function simpanPengajar()
    {
        $dataPengajar=array(
                'nip'=>$this->input->post('nip'),
                'nama'=>$this->input->post('nama'),
                'jenis_kelamin'=>$this->input->post('jenis_kelamin'),
                'tempat_lahir'=>$this->input->post('tempat_lahir'),
                'tgl_lahir'=>$this->input->post('tgl_lahir'),
                'alamat'=>$this->input->post('alamat')
        );//print_r($dataPengajar);
        $return_id=$this->Admin_model->insert($dataPengajar,'el_pengajar');
        $dataLogin=array(
            'username'=>$this->input->post('username'),
            'password'=>md5($this->input->post('password')),
            'pengajar_id'=>$return_id,
            'is_admin'=>$this->input->post('opsi')
        );
        $this->Admin_model->insert($dataLogin,'el_login');
        redirect('Admin/dataadmin/0');
    }
    public function Pesan()
    {
        $data['nama'] = $this->session->userdata('nama');
        $data['pesan']= $this->siswa_model->pesan($this->session->userdata('idLogin'))->result();
        $this->load->view('part/header');
        $this->load->view('part/sidebaradmin',$data);
        $this->load->view('admin/pesan',$data);
        $this->load->view('part/footer');
    }
    public function tambahPesan()
    {
        $data['nama'] = $this->session->userdata('nama');
        $data['tujuan']=$this->Admin_model->view_where('el_login',array('id !='=>$this->session->userdata('idLogin')))->result();
        // print_r($data);
        $this->load->view('part/header');
        $this->load->view('part/sidebaradmin',$data);
        $this->load->view('admin/tambahPesan',$data);
        $this->load->view('part/footer');
    }
    public function savePesan()
    {
        $values=array(
            'type_id'=>1,
            'content'=>$this->input->post('isiPesan'),
            'owner_id'=>$this->session->userdata('idLogin'),
            'sender_receiver_id'=>$this->input->post('tujuan'),
            'date'=>date('Y-m-d H:i:s'),
            'opened'=>0
        );
        // print_r($values);
        // echo $this->input->post('tujuan');
        $this->siswa_model->insert($values,'el_messages');
        redirect(base_url().'admin/detailPesan/'.$this->session->userdata('idLogin').'/'.$this->input->post('tujuan'));
    }
    public function detailPesan($send,$receive)
    {
        $data['nama'] = $this->session->userdata('nama');
        $penerima=$this->Admin_model->view_where('el_login',array('id'=>$receive))->result();
        $data['receiver']=$penerima[0]->id;
        $data['receiver_username']=$penerima[0]->username;
        $data['isi']=$this->siswa_model->isiPesan($send,$receive)->result();

        $this->load->view('part/header');
        $this->load->view('part/sidebaradmin',$data);
        $this->load->view('admin/detailPesan',$data);
        $this->load->view('part/footer');
    }
    public function kelas()
    {
        $data['data']=$this->Admin_model->getKelas()->result();
        $this->load->view('part/header');
        $this->load->view('part/sidebaradmin',$data);
        $this->load->view('admin/kelas',$data);
        $this->load->view('part/footer');
    }
    public function editKelas($id)
    {
        $kelas=$this->Admin_model->view_where('el_kelas',array('id'=>$id))->result();
        $data['namaKelas']=$kelas[0]->nama;
        $data['status']=$kelas[0]->aktif;
        $data['idkelas']=$kelas[0]->id;
        $data['data']=$this->Admin_model->getKelas()->result();
        $this->load->view('part/header');
        $this->load->view('part/sidebaradmin',$data);
        $this->load->view('admin/editKelas',$data);
        $this->load->view('part/footer');
    }
    public function tambahKelas()
    {
        $urutan=$this->Admin_model->getLastUrutanKelas()->result();
        // echo $urutan[0]->urutan+1;
        $data=array(
            'nama'=>$this->input->post('namaKelas'),
            'urutan'=>$urutan[0]->urutan+1,
            'aktif'=>'1'
        );
        $this->Admin_model->insert($data,'el_kelas');
        redirect('Admin/kelas');
    }
    public function updateKelas($id)
    {
        $new_status='';
        if ($this->input->post('status')=='') {
            $new_status=0;
        }else{
            $new_status=1;
        }
        $data=array(
            'nama'=>$this->input->post('namaKelas'),
            'aktif'=>$new_status
        );print_r($data);echo "$id";
        $this->Admin_model->update($data,array('id'=>$id),'el_kelas');
        redirect('Admin/kelas');
    }
    public function mapelKelas()
    {
        $data['data']=$this->Admin_model->getKelas()->result();
        $data['mapel']=$this->Admin_model->getMapelKelas()->result();
        $this->load->view('part/header');
        $this->load->view('part/sidebaradmin',$data);
        $this->load->view('admin/mapelKelas',$data);
        $this->load->view('part/footer');
    }
    public function aturMapelKelas($id)
    {
        $data['idkelas']=$id;
        $data['mapel']=$this->Admin_model->view_where('el_mapel',array('aktif'=>1))->result();
        $this->load->view('part/header');
        $this->load->view('part/sidebaradmin',$data);
        $this->load->view('admin/aturMapelKelas',$data);
        $this->load->view('part/footer');
    }
    public function simpanAturMapel($id)
    {
        $daftarMapel=$this->input->post('mapel');
        echo "<pre>";
        print_r($daftarMapel);
        echo "</pre>";
        for ($i=0; $i <count($daftarMapel) ; $i++) { 
            $data=array(
                'kelas_id'=>$id,
                'mapel_id'=>$daftarMapel[$i],
                'aktif'=>1
            );
            // print_r($data);
            $this->Admin_model->insert($data,'el_mapel_kelas');
        }
        redirect('Admin/mapelKelas');
    }
    public function hapusMapelKelas($id)
    {
        $data=array(
            'aktif'=>0
        );//echo $id;
        $this->Admin_model->update($data,array('id'=>$id),'el_mapel_kelas');
        redirect('Admin/mapelKelas');
    }
    public function hapusPesan($id,$sender,$receiver)
    {
        $this->pengajar_model->delete(array('id'=>$id),'el_messages');
        redirect('admin/detailPesan/'.$sender.'/'.$receiver);
    }

    public function jadwalMapel()
    {
        $data['data']=$this->Admin_model->getKelas()->result();
        $data['mapel']=$this->Admin_model->getMapelKelas()->result();
        $this->load->view('part/header');
        $this->load->view('part/sidebaradmin',$data);
        $this->load->view('admin/jadwalMapel/jadwal',$data);
        $this->load->view('part/footer');
    }

    public function LihatJadwalMapel($hari,$id)
    {   
        $data['hari'] = array("Senin","Selasa","Rabu","Kamis","Jumat");
        $data['idkelas']=$id;
        $data['day']=$hari;
        $data['jadwal']=$this->Admin_model->getJadwalKelas($hari,$id)->result();
        $data['kelas']=$this->Admin_model->getKelasId($id)->result();
        $this->load->view('part/header');
        $this->load->view('part/sidebaradmin',$data);
        $this->load->view('admin/jadwalMapel/jadwalmengajar',$data);
        $this->load->view('part/footer');
    }

    public function tambahJamMengajar($mapelkelas,$idkelas,$mapel)
    {
        $data['idkelas']=$idkelas;
        $data['idmapelkelas']=$mapelkelas;
        $data['kelas']=$this->Admin_model->getKelasId($idkelas)->result();
        $mapelid =$this->Admin_model->view_where('el_mapel_kelas',array('id'=>$mapelkelas))->result();
        $data['pengajar']=$this->Admin_model->view_where('el_pengajar',array('id_mapel'=>$mapelid[0]->mapel_id))->result();
        $data['mapel']=$this->Admin_model->view_where('el_mapel',array('id'=>$mapel))->result();
        $this->load->view('part/header');
        $this->load->view('part/sidebaradmin',$data);
        $this->load->view('admin/jadwalMapel/tambahJadwalKelas',$data);
        $this->load->view('part/footer');
    }
    public function editJamMengajar($id,$idmapelkelas,$mapelkelas)
    {
        $data['idkelas']=$id;
        $data['idmapelkelas']=$idmapelkelas;
        $data['mapel']=$this->Admin_model->getJadwalKelasId($idmapelkelas)->result();
        $mapelid =$this->Admin_model->view_where('el_mapel_kelas',array('id'=>$mapelkelas))->result();
        $data['kelas']=$this->Admin_model->getKelasId($id)->result();$this->load->view('part/header');
        $data['pengajar']=$this->Admin_model->view_where('el_pengajar',array('id_mapel'=>$mapelid[0]->mapel_id))->result();

        $this->load->view('part/sidebaradmin',$data);
        $this->load->view('admin/jadwalMapel/editJadwalkelas',$data);
        $this->load->view('part/footer');
    }

    public function prosesTambahJadwalMapel()
    {

        $hari = $this->input->post('hari');
        $start = $this->input->post('jammulai');
        $finish = $this->input->post('jamselesai');
        $pengajar = $this->input->post('pengajar');
        $mapelkelas = $this->input->post('idmapelkelas');
        $idkelas = $this->input->post('idkelas');
        
        $jadwal = array(
            'hari_id' => $hari, 
            'jam_mulai' => $start, 
            'jam_selesai' => $finish, 
            'pengajar_id' => $pengajar, 
            'mapel_kelas_id' => $mapelkelas, 
            'aktif' => 1, 
        );

        $this->Admin_model->insert($jadwal,'el_mapel_ajar');
        redirect('admin/LihatJadwalMapel/'.$hari.'/'.$idkelas);
    }
    public function prosesEditJadwalMapel()
    {

        $id = $this->input->post('idmapelajar');
        $start = $this->input->post('jammulai');
        $finish = $this->input->post('jamselesai');
        $pengajar = $this->input->post('pengajar');
        $mapelkelas = $this->input->post('idmapelkelas');
        $idkelas = $this->input->post('idkelas');
        
        $jadwal = array( 
            'jam_mulai' => $start, 
            'jam_selesai' => $finish, 
            'pengajar_id' => $pengajar, 
            'mapel_kelas_id' => $mapelkelas, 
            'aktif' => 1, 
        );

        $this->Admin_model->update($jadwal,array('id'=> $id),'el_mapel_ajar');
        redirect('admin/LihatJadwalMapel/1/'.$idkelas);
    }

    public function hapusJadwalMapel($id,$idkelas)
    {
        $this->Admin_model->delete(array('id'=>$id),'el_mapel_ajar');
        redirect('admin/LihatJadwalMapel/1/'.$idkelas);

    }

    public function jadwalMengajar($id)
    {
        $data['hari'] = array("Senin","Selasa","Rabu","Kamis","Jumat");
        $data['day']=$id;
        $data['jadwal'] = $this->admin_model->jadwalPelajaran($id,
        $this->session->userdata('id'))->result();    
        $this->load->view('part/header');
        $this->load->view('part/sidebaradmin');
        $this->load->view('admin/jadwalmengajar',$data);
        $this->load->view('part/footer');
    }
    public function ambilMapel()
    {
        $data['mapel'] = $this->admin_model->GetAllMapel()->result();
        $data['pengajar'] = $this->admin_model->getPengajar($this->session->userdata('id'))->result();
        $this->load->view('part/header');
        $this->load->view('part/sidebaradmin');
        $this->load->view('admin/ambilmatapelajaran',$data);
        $this->load->view('part/footer');
    }
    public function pickMapel($id)
    {
        $data = array('id_mapel' => $id);
        $this->admin_model->updateMapelPengajar($data,$this->session->userdata('id'));
        redirect('admin/ambilMapel');
    }

    public function tugas()
    {
        $data['data']=$this->admin_model->getKelas()->result();
        $data['pengajar']=$this->admin_model->getPengajar($this->session->userdata('id'))->result();
        $data['mapel']=$this->admin_model->getMapelKelas()->result();
        $this->load->view('part/header');
        $this->load->view('part/sidebaradmin',$data);
        $this->load->view('admin/tugas/tugaskelas');
        $this->load->view('part/footer');
    }
    public function listTugas($kelas,$mapelid)
        {
            $data['idkelas'] = $kelas;
            $data['mapelid'] = $mapelid;
            $data['materi']=$this->admin_model->getTugasKelas($kelas,$mapelid,$this->session->userdata('id'))->result();
            $this->load->view('part/header');
            $this->load->view('part/sidebaradmin');
            $this->load->view('admin/tugas/listtugas',$data);
            $this->load->view('part/footer');
        }
        public function tambahTugas($kelas,$mapelid)
        {
            $data['idkelas'] = $kelas;
            $data['idmapel'] = $mapelid;
            $data['kelas']=$this->admin_model->view_where('el_kelas',array('id' => $kelas))->result();
            $data['mapel']=$this->admin_model->view_where('el_mapel',array('id' => $mapelid))->result();

            $this->load->view('part/header');
            $this->load->view('part/sidebaradmin');
            $this->load->view('admin/tugas/TambahTugas',$data);
            $this->load->view('part/footer');
        }
        
        public function prosesUploadTugas()
        {
            $judul = $this->input->post('judul');
            $idkelas = $this->input->post('idkelas');
            $idmapel = $this->input->post('idmapel');
            $tanggal = $this->input->post('tanggal');
            $deadline = $this->input->post('deadline');
            $content = $this->input->post('isi');
            $todate = date('Y-m-d H:i:s', strtotime($deadline));

            $config['upload_path']          = './assets/tugas/';
            $config['allowed_types']        = '*';
    
            $this->load->library('upload', $config);
            $upload = $this->upload->do_upload('materi');
            if (!$upload){
                $data['idkelas'] = $idkelas;
                $data['error'] = $this->upload->display_errors();
                $data['idmapel'] = $idmapel;
                $data['kelas']=$this->admin_model->view_where('el_kelas',array('id' => $idkelas))->result();
                $data['mapel']=$this->admin_model->view_where('el_mapel',array('id' => $idmapel))->result();
                $this->load->view('part/header');
                $this->load->view('part/sidebaradmin');
                $this->load->view('admin/materi/TambahMateri',$data);
                $this->load->view('part/footer');
            }else{
                $upload = $this->upload->data();
                $data = array(
                    'mapel_id' => $idmapel,
                    'pengajar_id' =>$this->session->userdata('id'),
                    'tgl_buat' => $tanggal,
                    'judul' => $judul,
                    'durasi' => $todate,
                    'info' => $content,
                    'file' => $upload['file_name'],
                    'aktif' => 1,
                    'tampil_siswa' => 1
                );
                $id = $this->admin_model->insert($data,'el_tugas');
                $data = array('tugas_id' => $id,'kelas_id' =>$idkelas);
                $this->admin_model->insert($data,'el_tugas_kelas');
                redirect('admin/listTugas/'.$idkelas.'/'.$idmapel);
            }
        }

        public function detailTugas($idtugas)
        {
            $data['materi'] = $this->admin_model->view_where('el_tugas',array('id'=>$idtugas))->result();
            $this->load->view('part/header');
            $this->load->view('part/sidebaradmin');
            $this->load->view('admin/tugas/detailtugas',$data);
            $this->load->view('part/footer');
        }

        public function hapusTugas($id,$kelas,$mapelid)
        {
            $this->admin_model->hapusTugas($id);
            redirect("admin/listTugas/".$kelas."/".$mapelid);
        }
        public function penilaian($idtugas,$kelas,$mapelid)
        {
            $data['idkelas'] = $kelas;
            $data['idmapel'] = $mapelid;
            $data['idtugas'] = $idtugas;
            $data['upload'] = $this->admin_model->hasilUploadTugas($kelas,$idtugas)->result();
            $this->load->view('part/header');
            $this->load->view('part/sidebaradmin');
            $this->load->view('admin/tugas/listnilai',$data);
            $this->load->view('part/footer');

        }
        public function updateNilai($id,$idtugas,$kelas,$mapelid)
        {
            $data['idkelas'] = $kelas;
            $data['idmapel'] = $mapelid;
            $data['idtugas'] = $idtugas;

            $nilai = $this->input->post('nilai');
            $data = array('nilai' => $nilai );
            $this->admin_model->updateNilai($data,$id);
            redirect('admin/penilaian/'.$idtugas.'/'.$kelas.'/'.$mapelid);
        }
        public function materi()
        {
            $data['data']=$this->admin_model->getKelas()->result();
            $data['pengajar']=$this->admin_model->getPengajar($this->session->userdata('id'))->result();
            $data['mapel']=$this->admin_model->getMapelKelas()->result();
            $this->load->view('part/header');
            $this->load->view('part/sidebaradmin',$data);
            $this->load->view('admin/materi/materikelas');
            $this->load->view('part/footer');
        }

        public function listMateri($kelas,$mapelid)
        {
            $data['idkelas'] = $kelas;
            $data['mapelid'] = $mapelid;
            $data['materi']=$this->admin_model->getMateriKelas($kelas,$mapelid,$this->session->userdata('id'))->result();
            $this->load->view('part/header');
            $this->load->view('part/sidebaradmin');
            $this->load->view('admin/materi/listmateri',$data);
            $this->load->view('part/footer');
        }
        public function tambahMateri($kelas,$mapelid)
        {
            $data['idkelas'] = $kelas;
            $data['idmapel'] = $mapelid;
            $data['kelas']=$this->admin_model->view_where('el_kelas',array('id' => $kelas))->result();
            $data['mapel']=$this->admin_model->view_where('el_mapel',array('id' => $mapelid))->result();

            $this->load->view('part/header');
            $this->load->view('part/sidebaradmin');
            $this->load->view('admin/materi/TambahMateri',$data);
            $this->load->view('part/footer');
        }
        
        public function prosesUploadMateri()
        {
            $judul = $this->input->post('judul');
            $idkelas = $this->input->post('idkelas');
            $idmapel = $this->input->post('idmapel');
            $tanggal = $this->input->post('tanggal');
            $content = $this->input->post('isi');

            $config['upload_path']          = './assets/materi/';
            $config['allowed_types']        = '*';
    
            $this->load->library('upload', $config);
            $upload = $this->upload->do_upload('materi');
            if (!$upload){
                $data['idkelas'] = $idkelas;
                $data['error'] = $this->upload->display_errors();
                $data['idmapel'] = $idmapel;
                $data['kelas']=$this->admin_model->view_where('el_kelas',array('id' => $idkelas))->result();
                $data['mapel']=$this->admin_model->view_where('el_mapel',array('id' => $idmapel))->result();
                $this->load->view('part/header');
                $this->load->view('part/sidebaradmin');
                $this->load->view('admin/materi/TambahMateri',$data);
                $this->load->view('part/footer');
            }else{
                $upload = $this->upload->data();
                $data = array(
                    'mapel_id' => $idmapel,
                    'pengajar_id' =>$this->session->userdata('id'),
                    'tgl_posting' => $tanggal,
                    'judul' => $judul,
                    'konten' => $content,
                    'file' => $upload['file_name'],
                    'publish' => 1,
                    'views' => 1,
                );
                $id = $this->admin_model->insert($data,'el_materi');
                $data = array('materi_id' => $id,'kelas_id' =>$idkelas);
                $this->admin_model->insert($data,'el_materi_kelas');
                redirect('admin/listMateri/'.$idkelas.'/'.$idmapel);
            }
        }

        public function detailMateri($idtugas)
        {
            $data['materi'] = $this->admin_model->view_where('el_materi',array('id'=>$idtugas))->result();
            
            $this->load->view('part/header');
            $this->load->view('part/sidebaradmin');
            $this->load->view('admin/materi/detailmateri',$data);
            $this->load->view('part/footer');
        }
        public function download($nama)
        {
            $pth    =   file_get_contents(base_url()."assets/materi/".$nama);
            force_download($nama, $pth);
        }
        public function downloadTugas($nama)
        {
            $pth    =   file_get_contents(base_url()."assets/tugas/".$nama);
            force_download($nama, $pth);
        }

        public function hapusMateri($id,$kelas,$mapelid)
        {
            $this->admin_model->hapusMateri($id);
            redirect("admin/listMateri/".$kelas."/".$mapelid);
        }
}

?>