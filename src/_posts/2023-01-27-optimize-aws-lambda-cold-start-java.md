---
title: "Optimize AWS Lambda cold start (Java)"
date: 2023-01-27
tags: java lambda cold aws
---
Any problems with cold startup times in AWS Lambda using Java as programming language? Same here and without any special tricks
the performance remains bad. Check the image below showing a Lambda function from our production account. Quite clear when I
released the optimization, isn't it?

![Pre/Post performance of a Java Lambda](/assets/posts/20230127-lambda-metrics.png)

## Quarkus with Graal VM

I have heard that this is the ultimate solution. Should be very fast, but I didn't evaluate it. Quarkus needs some adjustments
in the pipelines we use, so out of scope for now. But we are working on this as well.

## Java Compiler Optimizations

I found the article [Optimizing AWS Lambda function performance for Java](https://aws.amazon.com/blogs/compute/optimizing-aws-lambda-function-performance-for-java/)
in the AWS blog. The authors describe how to use the tiered compilation of improve the performance. A very interesting article!

To condense everything: Add `JAVA_TOOL_OPTIONS = “-XX:+TieredCompilation -XX:TieredStopAtLevel=1”` to the environment variables
of your Lambda function and you are done. The cold start times were reduced by 50%.
