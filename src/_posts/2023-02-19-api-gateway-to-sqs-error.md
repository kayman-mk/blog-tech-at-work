---
comments_id: 117
title: "API Gateway SQS Integration Error"
date: 2023-02-19
tags: aws api gateway sqs error
---
Ever used the API Gateway with the SQS integration? This is a nice service to put API calls into an SQS queue
automatically and process them with a Lambda, ... But strange things happen if you have API calls sending more
than 256k data, what I didn't know at that time.

![Simple API Gateway setup](/assets/posts/20230219-api-gateway-sqs-lambda.png)

I used the following template to convert the data from GitLab webhooks into a SQS message:

<!-- markdownlint-disable MD033 -->
```hcl
resource "aws_api_gateway_integration" "gitlab_webhook_as_sqs" {
  rest_api_id             = aws_api_gateway_rest_api.gitlab_webhook.id
  resource_id             = aws_api_gateway_resource.gitlab_webhook.id
  http_method             = aws_api_gateway_method.gitlab_webhook.http_method
  type                    = "AWS"
  integration_http_method = "POST"
  credentials             = aws_iam_role.gitlab_webhook_api_gateway.arn
  uri                     = "arn:aws:apigateway:${data.aws_region.this.name}:sqs:path/${aws_sqs_queue.gitlab_webhook.name}"

  request_parameters = {
    "integration.request.header.Content-Type" = "'application/x-www-form-urlencoded'"
  }

  request_templates = {
    "application/json" = <<EOF
    Action=SendMessage&MessageBody={
  "method": "$context.httpMethod",
  "body-json" : $input.json('$'),
  "queryParams": {
    #foreach($param in $input.params().querystring.keySet())
    "$param": "$util.escapeJavaScript($input.params().querystring.get($param))" #if($foreach.hasNext),#end
  #end
  },
  "pathParams": {
    #foreach($param in $input.params().path.keySet())
    "$param": "$util.escapeJavaScript($input.params().path.get($param))" #if($foreach.hasNext),#end
    #end
  },
  "headerParams": {
    #foreach($param in $input.params().header.keySet())
    "$param": "$util.escapeJavaScript($input.params().header.get($param))" #if($foreach.hasNext),#end
    #end
  }
}"
EOF  }
}
```
<!-- markdownlint-enable MD033 -->

Simple and straight forward and working for most cases. But sometimes I saw a 500 error in GitLab and had no idea,
why. Checking the logs didn't give me a clue

```text
Execution failed due to configuration error: No match for output mapping and no default output mapping configured. Endpoint Response Status Code: 403
```

403 indicates an authentication error with SQS. But as I see 200 status codes and many messages in my queue, that's
definitely not the case here.

The solution was to send one of the failing requests manually. The same error appeared, but shortening the body
of the request produced a `200 OK`. What AWS was telling me here, was, your message is too large. Why do they report
a 403 error? I don't know.
