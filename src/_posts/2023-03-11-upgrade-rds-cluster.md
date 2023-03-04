---
title: "Upgrade a RDS cluster"
date: 2023-03-11
tags: AWS RDS database cluster
---
We did it yesterday after discovering that the read performance was bad. The cluster consists of 2 reader and 1
writer instance. The readers were swapping all the time due to lack of memory. So we decided to upgrade to a better
instance type. 

![Database Cluster Overview](/assets/posts/20230311_RDS_Management_Console.png)

During the non-peak hours we followed this procedure to upgrade our cluster with zero downtime:

1. Manually add a new reader instance as the instance being upgraded won't be available to the cluster. Make sure
   that the priority of the new instance is set to `tier-15` so it won't be elected as a new writer instance
   at failover.
2. Do the instance type upgrade manually in AWS Console (readers first). Instance by instance, one at a time.
3. As soon as you upgrade the writer instance, the cluster performs a failover to one of the readers.
4. Delete the manually added reader instance.
5. Change your Terraform code and make sure that the plan is clean (as the updates were done manually).

This change happened without any major problems. We found out that 20 requests to the application ended with error
(the load at that time was 4 requests/second). This is acceptable for us and it seems that the RDS proxy is a good
choice.

AWS Lambda functions didn't recover automatically. We saw many errors but were able to fix them with publishing a
new version. I think the database connection was shared between the invocations and never updated automatically
after the change.

Ok, seems to be simple with a cluster. But how to update a single DB instance? I guess this will be tricky as you
can't shutdown the instance (100% error rate guaranteed). But a few days ago I saw an interesting article:
[Creating a blue/green deployment](https://docs.aws.amazon.com/AmazonRDS/latest/UserGuide/blue-green-deployments.html).
Check it out. It seems that AWS found a method to do that, at least for some database types.
