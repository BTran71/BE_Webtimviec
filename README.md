url:http:://127.0.0.1:8000 or localhost:8000

url/registerCandidate: đăng ký của ứng viên
POST
key: name, email,password
url/registerEmployer :đăng ký cho nhà tuyển dụng
POST
key:
    'company_name'
            'email' 
            'password' 
            'phone_number'
            'address' 
            'company_size'
url/registerAdmin: đăng ký cho admin chạy trên postman thôi
POST
key:email,password
url/loginCandidate:đăng nhập cho ứng viên
POST
key:name,email,password

//đăng nhập key chung là email và password sử dụng POST
url/loginAdmin
url/loginCandidate
url/loginEmployer

//đăng xuất:
url/logout POST
Authorization: bỏ token vào
guard: tùy người đang đăng nhập là người nào (ứng viên:candidate,admin,employer:ntd)

//các chức năng cần admin có thể dử dụng khi đăng nhập
//CRUD dành cho lĩnh vực
id: là id của lĩnh vực
//hiện tất cả các lĩnh vực 
get('/admin/getIndustry')
//thêm
post('/admin/addIndustry')
key:industry_name
sửa
    put('/admin/updateIndustry/{id}')
    nhập industry_name
    xóa
    delete('/admin/deleteIndustry/{id}')
   tìm kiếm
    post('/admin/searchIndustry')
    bên headers bỏ key=Content-Type:application/json
    qua body chọn raw: nhập
    {
        "industry_name": "T"
    }

    //CRUD api nơi làm việc
    tương tự nhưng thay bằng 
    key:city
    
    get('/admin/getWorkplace)
    post('/admin/addWorkplace)
    put('/admin/updateWorkplace/{id})
    delete('/admin/deleteWorkplace/{id}')
    post('/admin/searchWorkplace')




