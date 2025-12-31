<?php 
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\NorkaModel;

class NorkaController extends BaseController {

   public function insurance() {
    $model = new NorkaModel();
    $builder = $model->where('type','insurance');

    $keyword = $this->request->getGet('keyword');
    $start   = $this->request->getGet('start_date');
    $end     = $this->request->getGet('end_date');

    if ($keyword) {
        $builder->groupStart()
                ->like('first_name', $keyword)
                ->orLike('last_name', $keyword)
                ->orLike('email', $keyword)
                ->orLike('mobile', $keyword)
                ->groupEnd();
    }

    if ($start && $end) {
        $builder->where("DATE(created_at) >=", $start)
                ->where("DATE(created_at) <=", $end);
    }

    $data['results'] = $builder->findAll();
    $data['title'] = "Norka Insurance Customers";
    return view('admin/norka/list', $data);
}

public function care() {
    $model = new NorkaModel();
    $builder = $model->where('type','care');

    $keyword = $this->request->getGet('keyword');
    $start   = $this->request->getGet('start_date');
    $end     = $this->request->getGet('end_date');

    if ($keyword) {
        $builder->groupStart()
                ->like('first_name', $keyword)
                ->orLike('last_name', $keyword)
                ->orLike('email', $keyword)
                ->orLike('mobile', $keyword)
                ->groupEnd();
    }

    if ($start && $end) {
        $builder->where("DATE(created_at) >=", $start)
                ->where("DATE(created_at) <=", $end);
    }

    $data['results'] = $builder->findAll();
    $data['title'] = "Norka Care Customers";
    return view('admin/norka/list', $data);
}

    public function update($id)
{
    $model = new NorkaModel();
    $file = $this->request->getFile('image');
    $imageName = $this->request->getPost('old_image'); // keep old if not changed

    if ($file && $file->isValid()) {
        $imageName = $file->getRandomName();
        $file->move(FCPATH . 'uploads/norka', $imageName);
    }

    $data = [
        'first_name'  => $this->request->getPost('first_name'),
        'middle_name' => $this->request->getPost('middle_name'),
        'last_name'   => $this->request->getPost('last_name'),
        'role'        => $this->request->getPost('role'),
        'dob'         => $this->request->getPost('dob'),
        'gender'      => $this->request->getPost('gender'),
        'mobile'      => $this->request->getPost('mobile'),
        'phone'       => $this->request->getPost('phone'),
        'email'       => $this->request->getPost('email'),
        'image'       => $imageName
    ];

    // update password only if provided
    if ($this->request->getPost('password')) {
        $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_BCRYPT);
    }

    $model->update($id, $data);

    return redirect()->back()->with('success', 'Customer Updated Successfully');
}


    public function save() {
        $model = new NorkaModel();
        $file = $this->request->getFile('image');
        $imageName = null;

        if ($file && $file->isValid()) {
            $imageName = $file->getRandomName();
            $file->move(FCPATH.'uploads/norka', $imageName);
        }

        $data = [
            'type'       => $this->request->getPost('type'),
            'first_name' => $this->request->getPost('first_name'),
            'middle_name'=> $this->request->getPost('middle_name'),
            'last_name'  => $this->request->getPost('last_name'),
            'role'       => $this->request->getPost('role'),
            'dob'        => $this->request->getPost('dob'),
            'gender'     => $this->request->getPost('gender'),
            'mobile'     => $this->request->getPost('mobile'),
            'phone'      => $this->request->getPost('phone'),
            'email'      => $this->request->getPost('email'),
            'password'   => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'image'      => $imageName
        ];

        $model->insert($data);
        return redirect()->back()->with('success', 'Customer Added Successfully');
    }

     public function delete($id) {
        $model = new NorkaModel();
        $model->delete($id);
        return redirect()->back()->with('success', 'Customer Deleted Successfully');
    }

    public function roots()
{
    $data['title'] = "Norka Roots Website";
    return view('admin/norka/roots', $data);
}
}