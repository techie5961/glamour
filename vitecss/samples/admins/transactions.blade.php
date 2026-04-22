@extends('layout.admins.app')
@section('title')
    Transaction History
@endsection
@section('main')
    <section class="column p-10 w-100 no-select g-10">
        <div class="card">
           <div class="row space-between align-center">
             <div class="column">
                <svg  xmlns="http://www.w3.org/2000/svg"  width="32"  height="32"  viewBox="0 0 24 24"  fill="none"  stroke="#4caf50"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-list-details"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M13 5h8" /><path d="M13 9h5" /><path d="M13 15h8" /><path d="M13 19h5" /><path d="M3 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M3 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /></svg>
            <span>Total Transactions</span>
            </div>
            <strong class="desc">20</strong>
           </div>
        </div>
         <div class="card">
           <div class="row space-between align-center">
             <div class="column">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#4caf50" viewBox="0 0 256 256"><path d="M208,32H184V24a8,8,0,0,0-16,0v8H88V24a8,8,0,0,0-16,0v8H48A16,16,0,0,0,32,48V208a16,16,0,0,0,16,16H208a16,16,0,0,0,16-16V48A16,16,0,0,0,208,32ZM72,48v8a8,8,0,0,0,16,0V48h80v8a8,8,0,0,0,16,0V48h24V80H48V48ZM208,208H48V96H208V208Zm-64-56a16,16,0,1,1-16-16A16,16,0,0,1,144,152Z"></path></svg>
                    <span>Todays Transactions</span>
            </div>
            <strong class="desc">10</strong>
           </div>
        </div>
         <div class="card">
           <div class="row space-between align-center">
             <div class="column">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#4caf50" viewBox="0 0 256 256"><path d="M208,48H48A24,24,0,0,0,24,72V184a24,24,0,0,0,24,24H208a24,24,0,0,0,24-24V72A24,24,0,0,0,208,48ZM40,96H216v16H160a8,8,0,0,0-8,8,24,24,0,0,1-48,0,8,8,0,0,0-8-8H40Zm8-32H208a8,8,0,0,1,8,8v8H40V72A8,8,0,0,1,48,64ZM208,192H48a8,8,0,0,1-8-8V128H88.8a40,40,0,0,0,78.4,0H216v56A8,8,0,0,1,208,192Z"></path></svg>
                      <span>Total Amount</span>
            </div>
            <strong class="desc">&#8358;10,000.00</strong>
           </div>
        </div>
        <div class="cont required">
            <div class="h-full perfect-square column align-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#4caf50" viewBox="0 0 256 256"><path d="M229.66,218.34l-50.07-50.06a88.11,88.11,0,1,0-11.31,11.31l50.06,50.07a8,8,0,0,0,11.32-11.32ZM40,112a72,72,0,1,1,72,72A72.08,72.08,0,0,1,40,112Z"></path></svg>
            </div>
            <input oninput="SearchRequest(this,'{{ url()->to('admins/search/transactions?search=true') }}')" type="search" placeholder="Search by username,Email or Full Name" class="inp input">
            <div class="search-result">
                <a href="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="CurrentColor" viewBox="0 0 256 256"><path d="M230.92,212c-15.23-26.33-38.7-45.21-66.09-54.16a72,72,0,1,0-73.66,0C63.78,166.78,40.31,185.66,25.08,212a8,8,0,1,0,13.85,8c18.84-32.56,52.14-52,89.07-52s70.23,19.44,89.07,52a8,8,0,1,0,13.85-8ZM72,96a56,56,0,1,1,56,56A56.06,56.06,0,0,1,72,96Z"></path></svg>
                    <span class="right-auto">Techie5961</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="CurrentColor" viewBox="0 0 256 256"><path d="M200,64V168a8,8,0,0,1-16,0V83.31L69.66,197.66a8,8,0,0,1-11.32-11.32L172.69,72H88a8,8,0,0,1,0-16H192A8,8,0,0,1,200,64Z"></path></svg>
                </a>
                
            </div>
        </div>
    </section>
    <section class="grid pc-grid-2 w-full g-10 p-10">
        <div class="card">
            <div class="row g-10">
                <div style="background-image:url('{{ asset('images/users/avatar.jpeg') }}')" class="photo"></div>
               <div class="column g-5">
                 <b><a class="c-text" href="">Techie5961</a></b>
                <span class="text-dim text-small row align-center g-5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="CurrentColor" viewBox="0 0 256 256"><path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm0,192a88,88,0,1,1,88-88A88.1,88.1,0,0,1,128,216Zm64-88a8,8,0,0,1-8,8H128a8,8,0,0,1-8-8V72a8,8,0,0,1,16,0v48h48A8,8,0,0,1,192,128Z"></path></svg>
                    Submitted 2 hours ago</span>
               </div>
               <div class="status green m-left-auto">success</div>
            </div>
            <hr>
            <div class="grid grid-2 g-10 break-word">
               <div class="column w-full g-5">
                 <span class="u m-right-auto">Bank Sent From</span>
                <b class="right-auto">Standard Chartered Bank</b>
               </div>
                <div class="column w-full g-5">
                 <span class="u m-left-auto">Name on Account</span>
                <b class="left-auto">Daniel Podence</b>
               </div>
            </div>
              <div class="grid grid-2 g-10 break-word">
               <div class="column w-full g-5">
                 <span class="u m-right-auto">Account Number</span>
                <b class="right-auto">5005016577</b>
               </div>
                <div class="column w-full g-5">
                 <span class="u m-left-auto">Class</span>
                <b class="left-auto">credit</b>
               </div>
            </div>
            <strong class="desc m-left-auto c-green">&#8358;50,000.00</strong>
            <hr>
            <div class="row space-between w-full g-10">
                 <button class="btn-green m-left-auto">
                    Approve
                </button>
                <button class="btn-red">
                    Reject
                </button>
            </div>
        </div>
        


         <div class="card">
            <div class="row g-10">
                <div style="background-image:url('{{ asset('images/users/avatar.jpeg') }}')" class="photo"></div>
               <div class="column g-5">
                 <b><a class="c-text" href="">Techie5961</a></b>
                <span class="text-dim text-small row align-center g-5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="CurrentColor" viewBox="0 0 256 256"><path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm0,192a88,88,0,1,1,88-88A88.1,88.1,0,0,1,128,216Zm64-88a8,8,0,0,1-8,8H128a8,8,0,0,1-8-8V72a8,8,0,0,1,16,0v48h48A8,8,0,0,1,192,128Z"></path></svg>
                    Submitted 2 hours ago</span>
               </div>
               <div class="status green m-left-auto">success</div>
            </div>
            <hr>
            <div class="grid grid-2 g-10 break-word">
               <div class="column w-full g-5">
                 <span class="u m-right-auto">Bank Sent From</span>
                <b class="right-auto">Standard Chartered Bank</b>
               </div>
                <div class="column w-full g-5">
                 <span class="u m-left-auto">Name on Account</span>
                <b class="left-auto">Daniel Podence</b>
               </div>
            </div>
              <div class="grid grid-2 g-10 break-word">
               <div class="column w-full g-5">
                 <span class="u m-right-auto">Account Number</span>
                <b class="right-auto">5005016577</b>
               </div>
                <div class="column w-full g-5">
                 <span class="u m-left-auto">Class</span>
                <b class="left-auto">credit</b>
               </div>
            </div>
            <strong class="desc m-left-auto c-green">&#8358;50,000.00</strong>
            <hr>
            <div class="row space-between w-full g-10">
                 <button class="btn-green m-left-auto">
                    Approve
                </button>
                <button class="btn-red">
                    Reject
                </button>
            </div>
        </div>
    </section>
@endsection