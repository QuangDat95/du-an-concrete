<div>
   <h2>Báo cáo khảo sát</h2>
   <p> Chào manager, có một khảo sát của khách hàng {{$data['customer_name']}} tại công trình {{$data['construction_name']}} ở địa chỉ {{$data['construction_address']}} có đánh 
      giá không tốt/không đạt tại hạng mục:</p>
   <ul>
      @foreach($data['list_bad_answers'] as $badAnswer)
         @if($badAnswer['description']=="")
            <li>{{$badAnswer['name']}}</li>
         @else
            <li>
               {{$badAnswer['name']}} <br>
               Nội dung: {{$badAnswer['description']}}
            </li>
         @endif
      @endforeach
   </ul>
   <p>Nhân viên khảo sát: {{$data['name']}}</p>
   <p>Email nhân viên khảo sát: {{$data['email_user']}}</p>
   <p>Vui lòng đăng nhập vào hệ thống để xem chi tiết đánh giá và xử lý.</p>
</div>