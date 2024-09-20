<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Student;
use CodeIgniter\HTTP\ResponseInterface;

class StudentController extends BaseController
{
       
    public function save()
    {

        if ($this->request->isAJAX()) {

            $validate = [
                'name' => [
                    'rules' => 'required|min_length[3]|max_length[100]',
                ],
                'subject' => [
                    'rules' => 'required|min_length[3]|max_length[100]',
                ],
                'mark' => [
                    'rules' => 'required|integer'
                ]
            ];

            if (!$this->validate($validate)) {
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'errors' => $this->validator->getErrors()
                ]);
            } else {

                $student = new Student();
                $exist = $student->asObject()->where(['name' => $this->request->getPost('name'), 'subject' => $this->request->getPost('subject')])->first();

                if ($exist) {

                    $student->where('id', $exist->id)->set(['mark' => $this->request->getPost('mark')])->update();
                    return response()->setJSON([
                        'status' => true,
                        'token' => csrf_hash(),
                        'message' => 'Student mark updated successfully.'
                    ]);

                } else {

                    $studentData = [
                        'name' => ucfirst($this->request->getPost('name')),
                        'subject' => ucfirst($this->request->getPost('subject')),
                        'mark' => $this->request->getPost('mark')
                    ];
                    $student->insert($studentData);
                    return response()->setJSON([
                        'status' => true,
                        'token' => csrf_hash(),
                        'message' => 'New student added successfully.'
                    ]);
                }
            }
        }
    }


   
    public function index()
    {
        $search = $this->request->getGet('search');
        $student = new Student();

        if(isset($search)){

            $data['students'] = $student->asObject()        
            ->like('name', $search)
            ->orLike('subject', $search)
            ->orLike('mark', $search)
            ->orderBy('id', 'DESC')
            ->paginate(5);

        }else{

            $data['students'] = $student->asObject()       
            ->orderBy('id', 'DESC')
            ->paginate(5);  
            
        }
        
                          
        $data['pager'] = $student->pager;
        return view('student/normal', $data);
    }

    public function action()
    {

        if ($this->request->isAJAX()) {

            if ($this->request->getPost('action') == 'edit') {

                $validate = [
                    'name' => [
                        'rules' => 'required|min_length[3]|max_length[100]',
                    ],
                    'subject' => [
                        'rules' => 'required|min_length[3]|max_length[100]',
                    ],
                    'marks' => [
                        'rules' => 'required|integer'
                    ]
                ];

                if (!$this->validate($validate)) {
                    return response()->setJSON([
                        'newToken' => csrf_hash(),
                        'errors' => $this->validator->getErrors()
                    ]);
                } else {

                    $student = new Student();
                    $student->where('id', $this->request->getPost('id'))
                        ->set([
                            'name' => $this->request->getPost('name'),
                            'subject' => $this->request->getPost('subject'),
                            'mark' => $this->request->getPost('marks')
                        ])
                        ->update();

                    return response()->setJSON([
                        'status' => true,
                        'newToken' => csrf_hash(),
                        'message' => 'Student details updated successfully.',
                        'data' => $this->request->getPost()
                    ]);
                }
            }


            if ($this->request->getPost('action') == 'delete') {

                $student = new Student();
                $student->where('id', $this->request->getPost('id'))->delete();

                return response()->setJSON([
                    'status' => true,
                    'newToken' => csrf_hash(),
                    'message' => 'Student data deleted successfully.',
                ]);
            }
        }
    }

}
