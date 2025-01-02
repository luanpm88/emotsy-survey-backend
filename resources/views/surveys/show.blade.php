@extends('layouts.main', [
    'menu' => 'survey',
])

@section('title', $survey->name)

@section('content')
    <script src="
    https://cdn.jsdelivr.net/npm/echarts@5.6.0/dist/echarts.min.js
    "></script>

    <div class="container mt-5">
        <h1 class="mb-4">{{ $survey->name }}</h1>
        <p>{{ $survey->question }}</p>
        <p>Type: {{ $survey->type }}</p>
        
        <hr>

        <div class="row">
            <div class="col-md-6">
                <h5>Results</h5>

                @if ($survey->ratings()->count())
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Result</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($survey->ratings as $rating)
                                <tr>
                                    <td>{{ $rating->user->name }}</td>
                                    <td>{{ $rating->user->email }}</td>
                                    <td>{{ $rating->result }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-info">There are no results yet!</div>
                @endif
            </div>
            <div class="col-md-6">
                <div id="main" style="width: 600px;height:400px;"></div>
                <script>
                    var chartDom = document.getElementById('main');
                    var myChart = echarts.init(chartDom);
                    var option;

                    option = {
                    title: {
                        text: '{{ $survey->name }}',
                        subtext: '{{ $survey->question }}',
                        left: 'center'
                    },
                    tooltip: {
                        trigger: 'item'
                    },
                    legend: {
                        orient: 'vertical',
                        left: 'left'
                    },
                    series: [
                        {
                        name: 'Access From',
                        type: 'pie',
                        radius: '50%',
                        data: {!! json_encode($chartData->map(function($data) {
                            return [
                                'value' => $data['value']+10,
                                'name' => $data['name'],
                            ];
                        })) !!},
                        emphasis: {
                            itemStyle: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                            }
                        }
                        }
                    ]
                    };

                    option && myChart.setOption(option);

                </script>
            </div>
        </div>
            

        <a href="{{ route('surveys.index') }}" class="btn btn-secondary">Back to Surveys</a>
    </div>
@endsection
