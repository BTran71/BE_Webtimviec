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

    //CRUD api ngôn ngữ
    tương tự nhưng thay bằng 
    key:language_name
    
    get('/admin/getLanguage)
    post('/admin/addLanguage)
    put('/admin/updateLanguage/{id})
    delete('/admin/deleteLanguage/{id}')
    post('/admin/searchLanguage')

    //CRUD api tin học
    tương tự nhưng thay bằng 
    key:name
    
    get('/admin/getIT)
    post('/admin/addIT)
    put('/admin/updateIT/{id})
    delete('/admin/deleteIT/{id}')
    post('/admin/searchIT')

    //quản lý tài khoản nhà tuyển dụng
    id: là id của nhà tuyển dụng
    put('/admin/{id}/changeLock'): 
    dùng để chuyển đổi giữa khóa và mở khóa tài khoản nhà tuyển dụng

    get('/admin/employer'): lấy thông tin của tất cả các tài khoản nhà tuyển dụng 

    post('/admin/employerSearch'): tìm kiếm tài khoản nhà tuyển dụng dựa trên key=company_name

    //quản lý tài khoản nhà tuyển dụng
    id: là id của nhà tuyển dụng
    put('/admin/{id}/changeLock'): 
    dùng để chuyển đổi giữa khóa và mở khóa tài khoản nhà tuyển dụng

    get('/admin/employer'): lấy thông tin của tất cả các tài khoản nhà tuyển dụng 

    post('/admin/employerSearch'): tìm kiếm tài khoản nhà tuyển dụng dựa trên key=company_name

    //CRUD api gói dịch vụ
    key chung cho thêm, sửa
            'name'
            'type' gồm 2 loại ưu tiên=1 và bình thường=0
            'price'
            'describe'
    
    get('/admin/getPosting)
    post('/admin/addPosting)
    put('/admin/updatePosting/{id})
    delete('/admin/deletePosting/{id}')
    post('/admin/searchPosting'): tìm kiếm gói dịch vụ dựa trên key=name

    //api profile
    post('/candidate/profile/add',[ProfileController::class,'addProfile']);
    key:
            'fullname'
            'email'
            'image':test ở form data chuyển kiểu text thành file rồi chọn file hình ảnh bỏ vào
            'phone_number'
            'gender': ghi Nam hoặc Nữ đúng chữ mà tui ghi nha 
            'skills'
            'day_ofbirth': ghi theo dạng dd-mm-yyyy
            'salary'
            'experience'
            'address'

            // Validation cho bảng liên quan
            
            'work_ex[0][company_name]'
            'work_ex[0][job_position]'
            'work_ex[0][start_time]' 
            'work_ex[0][end_time]' 
            'work_ex[0][description]'

           
            'reference[0][name]'
            'reference[0][company_name]'
            'reference[0][phone_number]'
            'reference[0][position]'

            
            'academy[0][schoolname]'
            'academy[0][major]'
            'academy[0][degree]'
            'academy[0][start_time]'
            'academy[0][end_time]'

            
            'languageDetails[0][level]'
			'languageDetails[0][language_id]'
			
			information_Details[0][it_id]
			
			workplaceDetails[0][workplace_id]
			
			industries[0][industry_id]
    put('/candidate/profile/update',[ProfileController::class,'updateProfile']);
	key:
			'fullname'
            'email'
            'image':test update mấy cái khác thôi nếu tạo profile mà chạy đc image thì sửa đc sửa không test được image
            'phone_number'
            'gender': ghi Nam hoặc Nữ đúng chữ mà tui ghi nha 
            'skills'
            'day_ofbirth': ghi theo dạng dd-mm-yyyy
            'salary'
            'experience'
            'address'

    put('/candidate/profile/lock'): chuyển trạng thái của profile nếu 0 thì là khóa hồ sơ còn 1 là không khóa

    //api kinh nghiệm
    get('/candidate/getWorkExperience');
    post('/candidate/addWorkExperience');
    put('/candidate/updateWorkExperience/{id}');
    delete('/candidate/deleteWorkExperience/{id}');

    //api công nghệ
    get('/candidate/getITDetails');
    post('/candidate/addITDetails');
    put('/candidate/updateITDetails/{id}');
    delete('/candidate/deleteITDetails/{id}');

    //api ngôn ngữ
    get('/candidate/getLanguageDetails');
    post('/candidate/addLanguageDetails');
    put('/candidate/updateLanguageDetails/{id}');
    delete('/candidate/deleteLanguageDetails/{id}');

    //api người liên lạc
    get('/candidate/getReference');
    post('/candidate/addReference');
    put('/candidate/updateReference/{id}');
    delete('/candidate/deleteReference/{id}');

    //api trường học
    get('/candidate/getAcademy');
    post('/candidate/addAcademy');
    put('/candidate/updateAcademy/{id}');
    delete('/candidate/deleteAcademy/{id}');

    //api ngành nghề
    get('/candidate/getIndustryProfile);
    post('/candidate/addIndustryProfile');
    put('/candidate/updateIndustryProfile/{id}');
    delete('/candidate/deleteIndustryProfile/{id}');

    //api nơi làm việc
    get('/candidate/getWorkplaceDetails');
    post('/candidate/addWorkplaceDetails');
    put('/candidate/updateWorkplaceDetails/{id}');
    delete('/candidate/deleteWorkplaceDetails/{id});

    //api tin tuyển dụng
    post('/search'): tìm kiếm theo title

    //api lọc tin
    get('/filter-jobs');
     
     lọc trong raw theo 
     vd:
    "industry_id": 1:id ngành nghề
    "salary_min": 5000,
    "salary_max": 15000,
    workplace_id:1: nơi làm việc

    //api lấy các tin chưa đến hạng
    get('/active-recruitments')

    //api lấy tin tuyển dụng theo id
    get('/get/news/{id}');

    //api thêm tin tuyển dụng
    post('/employer/add');
	key: các key có sẵn trong bảng recruitmentsnews và key các bảng đi kèm là 
	{bảng nghề nghiệp
	industry[0][industry_id]
	bảng nơi làm việc
	workplacenews[0][workplace_id]
	workplacenews[0][homeaddress]
	} 2 bảng này bắt buộc phải có hỏi khi thêm ông có làm thêm được nhiều bảng phụ không vậy
	bảng ngôn ngữ
	language[0][language_id]
	bảng tin học
	information[0][it_id]



