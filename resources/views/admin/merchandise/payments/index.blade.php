@extends('admin.layouts.merchandise')

@section('title', 'Payments | Merchandise')
@section('page_heading', 'Payments')
@section('page_subheading', 'Merchandise payment transactions')

@include('admin.master.payments.index', ['isEmbedded' => true, 'routePrefix' => 'admin.merchandise'])

